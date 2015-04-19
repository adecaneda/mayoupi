<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 19.04.15
 * Time: 10:55
 */

namespace Library;


class HTTPResponse {

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param $data mixed
     * @param $status int
     * @param $type string
     */
    public function __construct($data, $status = 200, $type = 'json')
    {
        $this->data = $data;
        $this->status = $status;
        $this->type = $type;
    }

    /**
     * @return mixed|string
     */
    public function build()
    {
        $status = http_response_code($this->status);

        if ($status != 200) {
            $output = json_encode(array('error' => $status));

        } else {
            if ($this->type === 'json') {
                header('Content-type: text/json');
                $output = json_encode($this->data);

            } else {  // html
                $output = $this->data;
            }
        }
        return $output;
    }
}