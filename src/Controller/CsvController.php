<?php



namespace App\Controller;

use App\Form\CsvUploadType;
use App\Service\CsvProcessor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CsvController extends AbstractController
{
    /**
     * @Route("/upload-csv", name="upload_csv")
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function uploadCsv(Request $request, CsvProcessor $csvProcessor): Response
    {
        $form = $this->createForm(CsvUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('csvFile')->getData();
            $csvData = $csvProcessor->processCsv($csvFile);

            return $this->render('csv/show.html.twig', [
                'csvData' => $csvData,
            ]);
        }

        return $this->render('csv/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
