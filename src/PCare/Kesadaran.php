<?php
namespace Barqdev\BpjsPcare\PCare;
use Barqdev\BpjsPcare\BpjsService;

class Kesadaran extends BpjsService
{
    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getKesadaran () {
        $response = $this->get('kesadaran');
        return json_decode($response, TRUE);
    }
}