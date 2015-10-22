<?php

/*
 * This file is part of the Kreta package.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kreta\Bundle\CommentBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Kreta\Component\Core\Annotation\ResourceIfAllowed as Issue;
use Kreta\SimpleApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class CommentController extends Controller
{
    /**
     * Returns all comments of issue id given, it admits date and owner filters, limit and offset.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request      The request
     * @param string                                    $issueId      The issue id
     * @param \FOS\RestBundle\Request\ParamFetcher      $paramFetcher The param fetcher
     *
     * @QueryParam(name="author", requirements="(.*)", strict=true, nullable=true, description="Author's email filter")
     * @QueryParam(name="createdOn", requirements="(.*)", strict=true, nullable=true, description="Created at filter")
     * @QueryParam(name="limit", requirements="\d+", default="9999", description="Amount of comments to be returned")
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Offset in pages")
     *
     * @ApiDoc(resource=true, statusCodes = {200, 403, 404})
     * @View(statusCode=200, serializerGroups={"commentList"})
     * @Issue()
     *
     * @return \Kreta\Component\Comment\Model\Interfaces\CommentInterface[]
     */
    public function getCommentsAction(Request $request, $issueId, ParamFetcher $paramFetcher)
    {
        return $this->get('kreta_comment.repository.comment')->findByIssue(
            $request->get('issue'),
            $paramFetcher->get('createdOn') ? new \DateTime($paramFetcher->get('createdOn')) : null,
            $paramFetcher->get('author'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );
    }

    /**
     * Creates new comment for description given.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request The request
     * @param string                                    $issueId The issue id
     *
     * @ApiDoc(statusCodes={201, 400, 403, 404})
     * @View(statusCode=201, serializerGroups={"comment"})
     * @Issue()
     *
     * @return \Kreta\Component\Comment\Model\Interfaces\CommentInterface
     */
    public function postCommentsAction(Request $request, $issueId)
    {
        return $this->get('kreta_comment.form_handler.comment')->processForm(
            $request, null, ['issue' => $request->get('issue')]
        );
    }

    /**
     * Updates the comment of issue id and comment id given.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request   The request
     * @param string                                    $issueId   The issue id
     * @param string                                    $commentId The comment id
     *
     * @ApiDoc(statusCodes={200, 400, 403, 404})
     * @View(statusCode=200, serializerGroups={"comment"})
     * @Issue()
     *
     * @return \Kreta\Component\Comment\Model\Interfaces\CommentInterface
     */
    public function putCommentsAction(Request $request, $issueId, $commentId)
    {
        $comment = $this->get('kreta_comment.repository.comment')->findByUser($commentId, $this->getUser());

        return $this->get('kreta_comment.form_handler.comment')->processForm(
            $request, $comment, ['method' => 'PUT', 'issue' => $request->get('issue')]
        );
    }
}
