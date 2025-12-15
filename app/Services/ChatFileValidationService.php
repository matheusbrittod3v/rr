<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class ChatFileValidationService
{
    /**
     * Tipos MIME permitidos
     */
    const ALLOWED_MIME_TYPES = [
        // Imagens
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        // Documentos
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        // Arquivos compactados
        'application/zip',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
        'application/x-tar',
        'application/gzip',
    ];

    /**
     * Extensões permitidas
     */
    const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
        'zip', 'rar', '7z', 'tar', 'gz'
    ];

    /**
     * Tamanho máximo em bytes (20MB)
     */
    const MAX_FILE_SIZE = 20 * 1024 * 1024;

    /**
     * Magic bytes para validação de tipo de arquivo
     */
    const MAGIC_BYTES = [
        'image/jpeg' => ["\xFF\xD8\xFF"],
        'image/png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'image/gif' => ["\x47\x49\x46\x38"],
        'image/webp' => ["RIFF", "WEBP"],
        'application/pdf' => ["%PDF"],
        'application/zip' => ["PK\x03\x04", "PK\x05\x06"],
        'application/x-rar-compressed' => ["Rar!\x1A\x07"],
    ];

    /**
     * Validar arquivo base64
     *
     * @param string $base64Data
     * @param string $fileName
     * @param string|null $declaredMimeType
     * @return array ['valid' => bool, 'error' => string|null, 'mime_type' => string|null]
     */
    public function validateBase64File(string $base64Data, string $fileName, ?string $declaredMimeType = null): array
    {
        try {
            // Decodificar base64
            $fileContent = base64_decode($base64Data, true);
            if ($fileContent === false) {
                return [
                    'valid' => false,
                    'error' => 'Dados base64 inválidos',
                    'mime_type' => null
                ];
            }

            // Validar tamanho
            $fileSize = strlen($fileContent);
            if ($fileSize > self::MAX_FILE_SIZE) {
                return [
                    'valid' => false,
                    'error' => 'Arquivo muito grande. Tamanho máximo: 20MB',
                    'mime_type' => null
                ];
            }

            if ($fileSize === 0) {
                return [
                    'valid' => false,
                    'error' => 'Arquivo vazio',
                    'mime_type' => null
                ];
            }

            // Validar extensão
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
                return [
                    'valid' => false,
                    'error' => 'Tipo de arquivo não permitido',
                    'mime_type' => null
                ];
            }

            // Detectar MIME type real usando magic bytes
            $detectedMimeType = $this->detectMimeTypeFromContent($fileContent, $extension);

            // Validar MIME type
            if (!$detectedMimeType || !in_array($detectedMimeType, self::ALLOWED_MIME_TYPES)) {
                return [
                    'valid' => false,
                    'error' => 'Tipo de arquivo não permitido ou corrompido',
                    'mime_type' => null
                ];
            }

            // Se foi declarado um MIME type, verificar se corresponde
            if ($declaredMimeType && $detectedMimeType !== $declaredMimeType) {
                Log::warning('MIME type declarado não corresponde ao detectado', [
                    'declared' => $declaredMimeType,
                    'detected' => $detectedMimeType,
                    'file' => $fileName
                ]);
                // Continuar, mas usar o detectado
            }

            return [
                'valid' => true,
                'error' => null,
                'mime_type' => $detectedMimeType,
                'size' => $fileSize
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao validar arquivo base64', [
                'error' => $e->getMessage(),
                'file' => $fileName
            ]);

            return [
                'valid' => false,
                'error' => 'Erro ao processar arquivo',
                'mime_type' => null
            ];
        }
    }

    /**
     * Detectar MIME type usando magic bytes
     *
     * @param string $fileContent
     * @param string $extension
     * @return string|null
     */
    protected function detectMimeTypeFromContent(string $fileContent, string $extension): ?string
    {
        // Verificar magic bytes
        foreach (self::MAGIC_BYTES as $mimeType => $signatures) {
            foreach ($signatures as $signature) {
                if (strpos($fileContent, $signature) === 0) {
                    return $mimeType;
                }
            }
        }

        // Fallback: usar extensão para tipos comuns
        $extensionMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
        ];

        return $extensionMap[$extension] ?? null;
    }

    /**
     * Validar arquivo UploadedFile
     *
     * @param UploadedFile $file
     * @return array
     */
    public function validateUploadedFile(UploadedFile $file): array
    {
        try {
            // Validar tamanho
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                return [
                    'valid' => false,
                    'error' => 'Arquivo muito grande. Tamanho máximo: 20MB'
                ];
            }

            // Validar extensão
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
                return [
                    'valid' => false,
                    'error' => 'Tipo de arquivo não permitido'
                ];
            }

            // Validar MIME type
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
                return [
                    'valid' => false,
                    'error' => 'Tipo de arquivo não permitido'
                ];
            }

            // Validar magic bytes
            $fileContent = file_get_contents($file->getRealPath());
            $detectedMimeType = $this->detectMimeTypeFromContent($fileContent, $extension);

            if (!$detectedMimeType || $detectedMimeType !== $mimeType) {
                return [
                    'valid' => false,
                    'error' => 'Arquivo corrompido ou tipo inválido'
                ];
            }

            return [
                'valid' => true,
                'error' => null,
                'mime_type' => $mimeType
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao validar arquivo', [
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'error' => 'Erro ao processar arquivo'
            ];
        }
    }
}

