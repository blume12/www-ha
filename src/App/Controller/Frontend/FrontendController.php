<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 13:20
 */

namespace App\Controller\Frontend;


use App\Controller\Controller;

abstract class FrontendController extends Controller
{
    /**
     * String to the frontend templates.
     *
     * @var string
     */
    protected $path = '/../../../templates/frontend/';
}