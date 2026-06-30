<?php

/**
 * Thin wrapper around PhpSpreadsheet for streaming .xlsx downloads.
 *
 * Mirrors the graceful-degradation pattern used elsewhere (see Receipt/DomPDF):
 * if PhpSpreadsheet is unavailable, falls back to a CSV download so exports
 * never hard-fail.
 */
class Spreadsheet {
    /**
     * Stream an .xlsx file to the browser and exit.
     *
     * @param array<int,string>        $headers Column header labels.
     * @param array<int,array<int,mixed>> $rows   Row data (arrays of cell values).
     * @param string                    $filename Download filename (without path).
     * @param string                    $title    Optional sheet title / caption.
     */
    public static function stream(array $headers, array $rows, string $filename, string $title = ''): void {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            self::streamCsv($headers, $rows, preg_replace('/\.xlsx$/i', '.csv', $filename));
            return;
        }

        $book = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $book->getActiveSheet();
        if ($title !== '') {
            // Sheet titles are limited to 31 chars and disallow certain symbols.
            $sheet->setTitle(substr(preg_replace('/[\\\\\/\?\*\[\]:]/', ' ', $title), 0, 31));
        }

        $rowIndex = 1;
        if ($headers) {
            $col = 1;
            foreach ($headers as $label) {
                $sheet->setCellValueExplicit(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $rowIndex,
                    (string) $label,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
                $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $rowIndex)
                    ->getFont()->setBold(true);
                $col++;
            }
            $rowIndex++;
        }

        foreach ($rows as $row) {
            $col = 1;
            foreach ($row as $value) {
                $sheet->setCellValue(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $rowIndex,
                    $value
                );
                $col++;
            }
            $rowIndex++;
        }

        foreach (range(1, max(1, count($headers))) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        if (!headers_sent()) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book);
        $writer->save('php://output');
        exit;
    }

    /** CSV fallback used when PhpSpreadsheet is not installed. */
    private static function streamCsv(array $headers, array $rows, string $filename): void {
        if (!headers_sent()) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        $out = fopen('php://output', 'w');
        if ($headers) {
            fputcsv($out, $headers);
        }
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }
}
