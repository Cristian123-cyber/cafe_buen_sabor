<?php

namespace App\Utils;

class ImageHandler
{
    private const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_FILE_SIZE = 5242880; // 5MB
    private const UPLOAD_DIR = __DIR__ . '/../../public/images/products/';
    private const BASE_URL = '/images/products/';

    /**
     * Sube una imagen y devuelve la URL
     */
    public static function uploadImage($file, $productId = null)
    {
        try {
            // Validar archivo
            self::validateFile($file);

            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = self::generateUniqueFilename($extension, $productId);

            // Ruta completa del archivo
            $filepath = self::UPLOAD_DIR . $filename;

            // Mover archivo
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \Exception('Error al mover el archivo subido');
            }

            // Devolver URL relativa
            return self::BASE_URL . $filename;

        } catch (\Exception $e) {
            throw new \Exception('Error al subir imagen: ' . $e->getMessage());
        }
    }

    /**
     * Valida un archivo de imagen
     */
    private static function validateFile($file)
    {
        // Verificar si se subió un archivo
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('No se recibió un archivo válido');
        }

        // Verificar tamaño
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \Exception('El archivo es demasiado grande. Máximo 5MB');
        }

        // Verificar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            throw new \Exception('Tipo de archivo no permitido. Solo JPG, PNG, GIF y WebP');
        }

        // Verificar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Extensión de archivo no permitida');
        }
    }

    /**
     * Genera un nombre único para el archivo
     */
    private static function generateUniqueFilename($extension, $productId = null)
    {
        $prefix = $productId ? "product_{$productId}_" : "product_";
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        
        return $prefix . $timestamp . '_' . $random . '.' . $extension;
    }

    /**
     * Elimina una imagen por URL
     */
    public static function deleteImage($imageUrl)
    {
        if (empty($imageUrl)) {
            return false;
        }

        // Extraer nombre del archivo de la URL
        $filename = basename($imageUrl);
        $filepath = self::UPLOAD_DIR . $filename;

        // Verificar que el archivo existe y está en el directorio correcto
        if (file_exists($filepath) && strpos(realpath($filepath), realpath(self::UPLOAD_DIR)) === 0) {
            return unlink($filepath);
        }

        return false;
    }

    /**
     * Actualiza una imagen (elimina la anterior y sube la nueva)
     */
    public static function updateImage($file, $oldImageUrl, $productId = null)
    {
        // Eliminar imagen anterior
        if (!empty($oldImageUrl)) {
            self::deleteImage($oldImageUrl);
        }

        // Subir nueva imagen
        return self::uploadImage($file, $productId);
    }

    /**
     * Verifica si una URL de imagen es válida
     */
    public static function isValidImageUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        // Verificar que la URL apunta a nuestro directorio
        if (strpos($url, self::BASE_URL) !== 0) {
            return false;
        }

        $filename = basename($url);
        $filepath = self::UPLOAD_DIR . $filename;

        return file_exists($filepath);
    }

    /**
     * Obtiene información de una imagen
     */
    public static function getImageInfo($imageUrl)
    {
        if (!self::isValidImageUrl($imageUrl)) {
            return null;
        }

        $filename = basename($imageUrl);
        $filepath = self::UPLOAD_DIR . $filename;

        $info = [
            'filename' => $filename,
            'size' => filesize($filepath),
            'mime_type' => mime_content_type($filepath),
            'created_at' => filectime($filepath),
            'url' => $imageUrl
        ];

        // Obtener dimensiones si es posible
        $imageInfo = getimagesize($filepath);
        if ($imageInfo) {
            $info['width'] = $imageInfo[0];
            $info['height'] = $imageInfo[1];
        }

        return $info;
    }

    /**
     * Limpia imágenes huérfanas (sin producto asociado)
     */
    public static function cleanupOrphanedImages()
    {
        $files = glob(self::UPLOAD_DIR . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $deletedCount = 0;

        foreach ($files as $file) {
            $filename = basename($file);
            
            // Verificar si el archivo tiene un patrón válido
            if (!preg_match('/^product_\d+_\d+_[a-f0-9]{16}\.(jpg|jpeg|png|gif|webp)$/', $filename)) {
                // Archivo no sigue el patrón, eliminarlo
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }
} 