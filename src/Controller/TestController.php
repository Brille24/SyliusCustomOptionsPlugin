<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 09.02.18
 * Time: 16:10
 */

namespace Brille24\CustomerOptionsPlugin\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function testAction(){
        return new Response('TEST!');
    }
}