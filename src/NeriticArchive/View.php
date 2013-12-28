<?php namespace NeriticArchive;
/**
 * jsonAPI - Slim extension to implement fast JSON API's
 *
 * @package Slim
 * @subpackage View
 * @author Jonathan Tavares <the.entomb@gmail.com>
 * @license GNU General Public License, version 3
 * @filesource
 *
 *
*/

/**
 * JsonApiView - view wrapper for json responses (with error code).
 * Modified to enable JSONP functionality
 *
 * @package Slim
 * @subpackage View
 * @author Jonathan Tavares <the.entomb@gmail.com>
 * @license GNU General Public License, version 3
 * @filesource
 */
class View extends \Slim\View {

    public function render($status=200, $data = NULL) {
        $app = \Slim\Slim::getInstance();

        $status = intval($status);

        $response = $this->all();

        //append error bool
        if (!$this->has('error')) {
            $response['error'] = false;
        }

        //append status code
        $response['status'] = $status;
        unset($response['flash']);

        $app->response()->status($status);
        $app->response()->header('Content-Type', 'application/json');
        $app->response()->body($this->jsonpWrap(json_encode($response)));

        $app->stop();
    }

    private function jsonpWrap($jsonp)
    {
        $app = \Slim\Slim::getInstance();
        if (($jsonCallback = $app->request()->get('callback')) !== null) {
            $jsonp = sprintf("%s(%s);", $jsonCallback, $jsonp);
            $app->response()->header('Content-type', 'application/javascript');
        }
        return $jsonp;
    }
}
