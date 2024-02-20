<?php
namespace App\Services;

use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Response;

class PdfGenerator
{
    private $dompdf;

    public function __construct()
    {
        $this->dompdf = new Dompdf();
    }

    public function generatePdfFromHtml(string $html): Response
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        $response = new Response($this->dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
