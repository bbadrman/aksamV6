<?

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExternalTableController extends AbstractController
{
    #[Route('/upload-table', name: 'upload_table', methods: ['GET', 'POST'])]
    public function uploadTable(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('csv_file');
            if ($file && $file->isValid()) {
                $data = $this->parseCsvFile($file->getPathname());
                return $this->render('external_table/index.html.twig', [
                    'data' => $data
                ]);
            }
        }

        return $this->render('external_table/upload.html.twig');
    }

    private function parseCsvFile(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }
}
