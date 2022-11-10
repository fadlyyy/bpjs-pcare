<?php
namespace Barqdev\BpjsPcare\PCare;
use Barqdev\BpjsPcare\BpjsService;

class Dokter extends BpjsService
{
    /**
     * @param $start
     * @param $limit
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDokter ($start, $limit) {
        $response = $this->get('dokter/' . $start . '/' . $limit);
        return json_decode($response, TRUE);
    }
}