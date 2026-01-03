<?php
/**
 * extract.php
 * Handles professional text extraction from PDF and Word documents.
 * Requires composer libraries: smalot/pdfparser and phpoffice/phpword
 */

require_once __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

class DocumentExtractor {
    
    /**
     * Extracts text from a file based on its mime type or extension.
     */
    public static function extract($filePath, $mimeType) {
        $text = "";
        
        try {
            if ($mimeType === 'application/pdf') {
                $text = self::parsePDF($filePath);
            } elseif (
                $mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
                pathinfo($filePath, PATHINFO_EXTENSION) === 'docx'
            ) {
                $text = self::parseDocx($filePath);
            }
        } catch (Exception $e) {
            error_log("Extraction error: " . $e->getMessage());
            return false;
        }

        return $text;
    }

    /**
     * Professional PDF Parsing using Smalot\PdfParser
     */
    private static function parsePDF($filePath) {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }

    /**
     * Professional Word Parsing using PHPWord
     */
    private static function parseDocx($filePath) {
        $phpWord = IOFactory::load($filePath);
        $fullText = "";
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $fullText .= $element->getText() . " ";
                } elseif (method_exists($element, 'getElements')) {
                    // Handle nested elements like tables or textruns
                    foreach ($element->getElements() as $childElement) {
                        if (method_exists($childElement, 'getText')) {
                            $fullText .= $childElement->getText() . " ";
                        }
                    }
                }
            }
        }
        return $fullText;
    }
}