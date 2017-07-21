<?php namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Model\User;

class NugetRequest {
    private static $inputCache = false;

    /**
     * NugetRequest constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return null|User
     */
    public function getUser()
    {
        $apiKey = $this->request->header('X-Nuget-Apikey');

        if (empty($apiKey))
        {
            return null;
        }

        return User::fromApiKey($apiKey);
    }

    private function getInput()
    {
        if(self::$inputCache === false)
        {
            self::$inputCache = file_get_contents('php://input'); 
        }

        return self::$inputCache;
    }

    private function getFileBlock($name)
    {
        // Read boundary indicator from content type.
        preg_match('/boundary=(.*)$/', $this->request->header('Content-Type'), $boundary);
        if (!count($boundary))
        {
            return false;
        }
        
        $boundary = trim($boundary[1], '"');

        // Split request body into blocks.
        $blocks = preg_split("/-+{$boundary}/", $this->getInput());

        // Strip last block. This block is always empty because it is after the last terminator.
        array_pop($blocks);

        // Check each blocks name for the name we are looking for.
        foreach ($blocks as $block)
        {
            // Skip blocks which are not octet streams.
            if (empty($block) || strpos($block, 'application/octet-stream') === false)
            {
                continue;
            }

            // Match the name.
            preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $nameAndStream);
            
            if(count($nameAndStream) != 3)
            {
                preg_match("/name=([^;]*?);[^\n^\r]*[\n|\r]+([^\n\r].*)$/s", $block, $nameAndStream);
            }
            
            if(count($nameAndStream) != 3)
            {
                continue;
            }
            
            $streamName = $nameAndStream[1];
            $stream = $nameAndStream[2];

            if ($name != $streamName || strlen($stream) <= 0)
            {
                continue;
            }

            // Strip terminators.
            $check = unpack('C*', substr($stream, -2));
            if ($check[1] == 13 && $check[2] == 10)
            {
                $stream = substr($stream, 0, -2);
            }

            return $stream;
        }

        return false;
    }

    public function hasUploadedFile($name)
    {
        return $this->getFileBlock($name) !== false;
    }

    public function getUploadedFile($name)
    {
        $stream = $this->getFileBlock($name);

        if ($stream === false)
        {
            return false;
        }

        $tmpPath = tempnam(sys_get_temp_dir(), '');
        file_put_contents($tmpPath, $stream);

        return $tmpPath;
    }
}
