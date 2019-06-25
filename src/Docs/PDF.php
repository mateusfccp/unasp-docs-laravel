<?php
    
namespace unaspbr\Docs;

use setasign\Fpdi\Tcpdf\Fpdi;

class PDF extends Fpdi {
    public static function from ($files = [])
    {
        $pdf = new Self();

        foreach ($files as $file) {
            $pageCount = $pdf->setSourceFile($file);

            foreach (range(1, $pageCount) as $page) {
                $page_id = $pdf->ImportPage($page);
                $size = $pdf->getTemplatesize($page_id);
                $pdf->AddPage($size['orientation'], $size);
                $pdf->useImportedPage($page_id);
            }
        }

        return $pdf;
    }
}
