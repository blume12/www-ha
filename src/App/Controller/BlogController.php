<?php

/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 11:19
 */

namespace App\Controller;

use App\Model\Blog\Blog;
use App\Model\Blog\BlogComment;
use Symfony\Component\HttpFoundation\Request;


class BlogController extends Controller
{

    /**
     * Loads the action for the blog list.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $blog = new Blog($this->getConfig());

        $this->setTemplateName('blog-list');
        $this->setPageTitle('Programme');

        return $this->getResponse(['data' => $blog->loadData()]);
    }

    /**
     * Loads the action for the blog detail page.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction(Request $request)
    {
        $blog = new Blog($this->getConfig());
        $blogId = $request->attributes->get('id');

        $data = $blog->loadSpecificEntry($blogId);

        $blogComment = new BlogComment($this->getConfig(), $blogId);
        $commentData = $blogComment->getComment();

        $this->setTemplateName('blog-detail');
        $this->setPageTitle('Programmdetails');

        return $this->getResponse(['data' => $data, 'commentData' => $commentData]);
    }
}