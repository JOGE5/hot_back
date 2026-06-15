<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReciboReservacionMail;
use App\Models\Reservacion;
use App\Support\LogSistema;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ReciboReservacionController extends Controller
{
    /**
     * Generar CUF simulado para fines académicos.
     * Formato: NIT(9) + FECHA(8) + HORA(4) + FACTURA(10) + SUCURSAL(4) + PUNTO_VENTA(7) + HASH(12)
     * Total: 54 caracteres
     */
    private function generarCufSimulado(Reservacion $reservacion): string
    {
        $nit = '000000000'; // NIT de prueba
        $fecha = now()->format('Ymd'); // YYYYMMDD
        $hora = now()->format('Hi'); // HHMM
        $numeroFactura = str_pad($reservacion->id, 10, '0', STR_PAD_LEFT);
        $sucursal = '0001'; // Sucursal por defecto
        $puntoVenta = str_pad(1, 7, '0', STR_PAD_LEFT); // Punto de venta por defecto
        
        // Generar hash usando datos de la reservación
        $datosHash = $nit . $fecha . $hora . $numeroFactura . $reservacion->total . $reservacion->codigo_checkin;
        $hash = strtoupper(substr(hash('sha256', $datosHash), 0, 12));
        
        return $nit . $fecha . $hora . $numeroFactura . $sucursal . $puntoVenta . $hash;
    }

    private function obtenerLogoBase64(): ?string
    {
        $paths = [
            public_path('images/logo-factura.png'),
            public_path('img/logo-factura.png'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $content = file_get_contents($path);
                if ($content !== false) {
                    return 'data:image/png;base64,' . base64_encode($content);
                }
            }
        }

        return null;
    }

    /**
     * Preparar todos los datos necesarios para generar la factura (misma fuente para descarga y correo)
     *
     * @return array
     */
    private function prepararDatosFactura(Reservacion $reservacion): array
    {
        // Fecha estable: usar fecha de pago si existe, sino created_at
        $fechaEmision = null;
        if (!empty($reservacion->fecha_pago)) {
            $fechaEmision = \Carbon\Carbon::parse($reservacion->fecha_pago);
        } else {
            $fechaEmision = \Carbon\Carbon::parse($reservacion->created_at);
        }

        // Número de factura (10 dígitos)
        $numeroFactura = str_pad($reservacion->id, 10, '0', STR_PAD_LEFT);

        // Generar CUF académico estilo SIAT
        $nitEmisor = str_pad('0', 13, '0'); // NIT emisor de prueba (13 dígitos)
        $fechaHora17 = $fechaEmision->format('YmdHis') . sprintf('%03d', (int) floor($fechaEmision->micro / 1000)); // yyyyMMddHHmmssSSS
        $sucursal = str_pad('1', 4, '0', STR_PAD_LEFT);
        $modalidad = '1';
        $tipoEmision = '1';
        $tipoFactura = '1';
        $tipoDocumentoSector = '01';
        $puntoVenta = str_pad('1', 4, '0', STR_PAD_LEFT);

        $raw = $nitEmisor . $fechaHora17 . $sucursal . $modalidad . $tipoEmision . $tipoFactura . $tipoDocumentoSector . $numeroFactura . $puntoVenta; // 53

        // Módulo 11 (algoritmo SIAT académico)
        $dv = $this->modulo11($raw);
        $rawConDv = $raw . $dv; // ahora 54

        // Convertir string decimal grande a hexadecimal (división manual)
        $hex = $this->decimalStringToHex($rawConDv);

        // Código de control académico fijo
        $codigoControl = 'A19E23EF34124CD';

        $cuf = strtoupper($hex . $codigoControl);

        // QR corto y seguro: usar fragmentos del CUF para evitar overflow
        $cufQr = substr($cuf, 0, 20) . substr($cuf, -12);

        // Logo
        $logoBase64 = $this->obtenerLogoBase64();

        // Responsable
        $responsable = $this->obtenerResponsable();

        // Monto literal
        $montoLiteral = $this->montoLiteral($reservacion->total);

        // Generar QR base64 (intentar, si falla devolver null)
        $qrBase64 = null;
        try {
            $qrBase64 = $this->generarQrBase64Compact($reservacion, $cufQr, $numeroFactura);
        } catch (Throwable $e) {
            $qrBase64 = null;
        }

        return [
            'numeroFactura' => $numeroFactura,
            'fechaEmision' => $fechaEmision,
            'cuf' => $cuf,
            'cufQr' => $cufQr,
            'qrBase64' => $qrBase64,
            'qrDisponible' => !empty($qrBase64),
            'logoBase64' => $logoBase64,
            'responsable' => $responsable,
            'montoLiteral' => $montoLiteral,
        ];
    }

    private function modulo11(string $numero): int
    {
        $suma = 0;
        $factor = 2;
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $digit = (int) $numero[$i];
            $suma += $digit * $factor;
            $factor++;
            if ($factor > 9) {
                $factor = 2;
            }
        }

        $resto = $suma % 11;
        $resultado = 11 - $resto;
        if ($resultado === 11) {
            return 0;
        }
        if ($resultado === 10) {
            return 1;
        }
        return $resultado;
    }

    /**
     * Convertir decimal (string muy largo) a hexadecimal usando división manual.
     */
    private function decimalStringToHex(string $decimal): string
    {
        $dec = ltrim($decimal, '0');
        if ($dec === '') {
            return '0';
        }

        $hex = '';
        while ($dec !== '') {
            $carry = 0;
            $new = '';
            for ($i = 0, $len = strlen($dec); $i < $len; $i++) {
                $digit = (int) $dec[$i];
                $num = $carry * 10 + $digit;
                $q = intdiv($num, 16);
                $carry = $num % 16;
                if ($new !== '' || $q !== 0) {
                    $new .= (string) $q;
                }
            }
            $hex = strtoupper(dechex($carry)) . $hex;
            $dec = $new;
        }

        return $hex;
    }

    /**
     * Generador QR compacto (payload reducido) devuelve base64 o null
     */
    private function generarQrBase64Compact(Reservacion $reservacion, string $cufQr, string $numeroFactura): ?string
    {
        $payload = sprintf('NIT=%s|F=%s|T=%.2f|R=%s', str_pad('0', 13, '0'), $numeroFactura, $reservacion->total, $reservacion->codigo_checkin);

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 4,
            'imageBase64' => false,
            'margin' => 1,
        ]);

        $qrCode = new QRCode($options);
        $pngData = $qrCode->render($payload);

        return 'data:image/png;base64,' . base64_encode($pngData);
    }

    private function generarQrBase64(Reservacion $reservacion, string $cuf, string $numeroFactura): ?string
    {
        $cufQr = substr($cuf, 0, 20) . substr($cuf, -12);
        $datosQr = sprintf(
            '000000000|%s|%s|%.2f|%s',
            $numeroFactura,
            $cufQr,
            $reservacion->total,
            $reservacion->codigo_checkin
        );

        try {
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 4,
                'imageBase64' => false,
                'margin' => 1,
            ]);

            $qrCode = new QRCode($options);
            $pngData = $qrCode->render($datosQr);

            return 'data:image/png;base64,' . base64_encode($pngData);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function numeroALetras(int $numero): string
    {
        $unidades = [
            '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
            'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciseis', 'diecisiete', 'dieciocho', 'diecinueve'
        ];

        $decenas = [
            '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
        ];

        $centenas = [
            '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
        ];

        if ($numero === 0) {
            return 'cero';
        }

        if ($numero === 100) {
            return 'cien';
        }

        if ($numero < 20) {
            return $unidades[$numero];
        }

        if ($numero < 100) {
            $decena = intdiv($numero, 10);
            $unidad = $numero % 10;
            $texto = $decenas[$decena];
            if ($unidad > 0) {
                if ($decena === 2) {
                    return 'veinti' . $unidades[$unidad];
                }
                return $texto . ' y ' . $unidades[$unidad];
            }
            return $texto;
        }

        if ($numero < 1000) {
            $centena = intdiv($numero, 100);
            $resto = $numero % 100;
            $texto = $centenas[$centena];
            if ($resto > 0) {
                return $texto . ' ' . $this->numeroALetras($resto);
            }
            return $texto;
        }

        if ($numero < 1000000) {
            $miles = intdiv($numero, 1000);
            $resto = $numero % 1000;
            $texto = $miles === 1 ? 'mil' : $this->numeroALetras($miles) . ' mil';
            if ($resto > 0) {
                return $texto . ' ' . $this->numeroALetras($resto);
            }
            return $texto;
        }

        $millones = intdiv($numero, 1000000);
        $resto = $numero % 1000000;
        $texto = $millones === 1 ? 'un millon' : $this->numeroALetras($millones) . ' millones';
        if ($resto > 0) {
            return $texto . ' ' . $this->numeroALetras($resto);
        }
        return $texto;
    }

    private function montoLiteral(float $monto): string
    {
        $enteros = (int) floor($monto);
        $centavos = (int) round(($monto - $enteros) * 100);
        $textoEnteros = $this->numeroALetras($enteros);
        $textoEnteros = ucfirst(trim($textoEnteros));

        return sprintf('%s %02d/100 Bolivianos', $textoEnteros, $centavos);
    }

    private function obtenerResponsable(): array
    {
        /** @var \App\Models\User|null $usuario */
        $usuario = Auth::user();

        if (! $usuario) {
            return [
                'nombre' => 'Usuario no disponible',
                'rol' => 'Rol no disponible',
            ];
        }

        $usuario->loadMissing('role');

        $nombre = trim(
            ($usuario->nombres ?? '') . ' ' .
            ($usuario->apellido_paterno ?? '') . ' ' .
            ($usuario->apellido_materno ?? '')
        );

        if ($nombre === '') {
            $nombre = $usuario->name ?? 'Usuario no disponible';
        }

        return [
            'nombre' => $nombre,
            'rol' => $usuario->role->nombre ?? 'Rol no disponible',
        ];
    }

    /**
     * Descargar el recibo de reservación en PDF (FACTURA).
     */
    public function generarPdf(Request $request, Reservacion $reservacion)
    {
        // Reglas estrictas: estado_pago = Confirmado, total > 0, codigo_checkin existe
        if ($reservacion->estado_pago !== 'Confirmado' || $reservacion->total <= 0 || empty($reservacion->codigo_checkin)) {
            return redirect()->back()->with('error', 'La reservación no cumple con los requisitos para generar una factura.');
        }

        // Cargar relaciones necesarias si no están cargadas
        $reservacion->loadMissing(['huesped', 'habitacion']);

        // Preparar datos de la factura (misma fuente para descarga y correo)
        $factura = $this->prepararDatosFactura($reservacion);

        // Asignar variables para mantener compatibilidad con la vista existente
        $cuf = $factura['cuf'];
        $numeroFactura = $factura['numeroFactura'];
        $logoBase64 = $factura['logoBase64'];
        $qrBase64 = $factura['qrBase64'];
        $responsable = $factura['responsable'];
        $montoLiteral = $factura['montoLiteral'];

        $pdf = Pdf::loadView('reportes.recibo_reservacion_pdf', compact(
            'reservacion',
            'cuf',
            'numeroFactura',
            'logoBase64',
            'qrBase64',
            'responsable',
            'montoLiteral'
        ))
                  ->setPaper([0, 0, 226.77, 850.39], 'portrait'); // 80mm x 300mm aprox

        LogSistema::registrar(
            'GENERAR_RECIBO_PDF',
            'Recibos',
            'Factura PDF generada/descargada para reservación #' . $reservacion->id . ' con código ' . $reservacion->codigo_checkin . '.'
        );

        return $pdf->download('factura_' . $reservacion->codigo_checkin . '.pdf');
    }

    /**
     * Enviar la factura de reservación al correo del huésped.
     *
     * Condiciones previas:
     *   - estado_pago = Confirmado
     *   - total > 0
     *   - codigo_checkin no es null
     *   - huesped existe y tiene correo_electronico
     */
    public function enviarCorreo(Reservacion $reservacion)
    {
        // --- Validaciones de negocio ---
        if ($reservacion->estado_pago !== 'Confirmado') {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if ($reservacion->total <= 0) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if (empty($reservacion->codigo_checkin)) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        // Cargar relaciones si no están disponibles
        $reservacion->loadMissing(['huesped', 'habitacion']);

        if (! $reservacion->huesped) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if (empty($reservacion->huesped->correo_electronico)) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        // --- Generar PDF en memoria (sin guardar en disco) ---
        $nombreArchivo = 'factura_' . $reservacion->codigo_checkin . '.pdf';
        
        // Preparar datos de la factura (misma fuente para descarga y correo)
        $factura = $this->prepararDatosFactura($reservacion);

        // Asignar variables para compatibilidad con la vista
        $cuf = $factura['cuf'];
        $numeroFactura = $factura['numeroFactura'];
        $logoBase64 = $factura['logoBase64'];
        $qrBase64 = $factura['qrBase64'];
        $responsable = $factura['responsable'];
        $montoLiteral = $factura['montoLiteral'];

        $pdfData = Pdf::loadView('reportes.recibo_reservacion_pdf', compact(
                     'reservacion',
                     'cuf',
                     'numeroFactura',
                     'logoBase64',
                     'qrBase64',
                     'responsable',
                     'montoLiteral'
                 ))
                     ->setPaper([0, 0, 226.77, 850.39], 'portrait') // 80mm x 300mm aprox
                     ->output(); // contenido binario en memoria

        // --- Enviar correo con el PDF adjunto ---
        try {
            Mail::to($reservacion->huesped->correo_electronico)
                ->send(new ReciboReservacionMail($reservacion, $pdfData, $nombreArchivo));
        } catch (Throwable $e) {
            return redirect()->back()->with(
                'error',
                'No se pudo enviar la factura. Revise la configuración del correo.'
            );
        }

        LogSistema::registrar(
            'ENVIAR_RECIBO_CORREO',
            'Recibos',
            'Factura enviada por correo a ' . $reservacion->huesped->correo_electronico . ' para reservación #' . $reservacion->id . '.'
        );

        return redirect()->back()->with(
            'success',
            'Factura enviada correctamente al correo del huésped.'
        );
    }
}
