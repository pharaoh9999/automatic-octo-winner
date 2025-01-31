<?php
use Dompdf\Dompdf;

class PDFGenerator {
    private $dompdf;

    public function __construct() {
        $this->dompdf = new Dompdf();
    }

    public function generatePDF($htmlContent, $outputFileName) {
        $this->dompdf->loadHtml($htmlContent);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream($outputFileName, ["Attachment" => true]);
    }
}

?>
