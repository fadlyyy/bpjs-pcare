<?php
namespace Barqdev\BpjsPcare\PCare;
use Barqdev\BpjsPcare\BpjsService;

class Diagnosa extends BpjsService
{
    /**
     * @param $keyword
     * @param $start
     * @param $limit
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDiagnosa ($keyword, $start, $limit) {
        $response = $this->get('diagnosa/' . $keyword . '/' . $start . '/' . $limit);
        return json_decode($response, TRUE);
    }
}