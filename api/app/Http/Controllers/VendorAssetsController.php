<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VendorAssetsController extends Controller
{
    public function show(string $asset)
    {
        $path = public_path('vendor/' . $asset);

        if (!file_exists($path)) {
            throw new NotFoundHttpException();
        }

        return response()->file($path, [
            'Content-Type' => $this->getContentType($path),
        ]);
    }

    private function getContentType(string $path): string
    {
       $ext = File::extension($path);

       if ($ext === 'css') {
           return 'text/css';
       }

       if ($ext === 'js') {
           return 'application/javascript';
       }

       return 'text/plain';
    }
}
