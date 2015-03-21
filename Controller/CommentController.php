<?php

/**
 * This file belongs to Kreta.
 * The source code of application includes a LICENSE file
 * with all information about license.
 *
 * @author benatespina <benatespina@gmail.com>
 * @author gorkalaucirica <gorka.lauzirika@gmail.com>
 */

namespace Kreta\Bundle\CommentBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Kreta\Bundle\CoreBundle\Controller\RestController;
use Kreta\SimpleApiDocBundle\Annotation\ApiDoc;

/**
 * Class CommentController.
 *
 * @package Kreta\Bundle\CommentBundle\Controller
 */
class CommentController extends RestController
{
    /**
     * Returns all comments of issue id given, it admits date and owner filters, limit and offset.
     *
     * @param string                               $issueId      The issue id
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher The param fetcher
     *
     * @QueryParam(name="owner", requirements="(.*)", strict=true, nullable=true, description="Owner's email filter")
     * @QueryParam(name="createdAt", requirements="(.*)", strict=true, nullable=true, description="Created at filter")
     * @QueryParam(name="limit", requirements="\d+", default="9999", description="Amount of comments to be returned")
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Offset in pages")
     *
     * @ApiDoc(resource=true, statusCodes = {200, 403, 404})
     * @View(statusCode=200, serializerGroups={"commentList"})
     *
     * @return \Kreta\Component\Comment\Model\Interfaces\CommentInterface[]
     */
    public function getCommentsAction($issueId, ParamFetcher $paramFetcher)
    {
        if ($createdAt = $paramFetcher->get('createdAt')) {
            $createdAt = new \DateTime($paramFetcher->get('createdAt'));
        };

        return $this->get('kreta_comment.repository.comment')->findByIssue(
            $this->getIssueIfAllowed($issueId),
            $createdAt,
            $paramFetcher->get('owner'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );
    }

    /**
     * Creates new comment for description given.
     *
     * @param string $issueId The issue id
     *
     * @ApiDoc(statusCodes={201, 400, 403, 404})
     * @View(statusCode=201, serializerGroups={"comment"})
     *
     * @return \Kreta\Component\Comment\Model\Interfaces\CommentInterface
     */
    public function postCommentsAction($issueId)
    {
        $issue = $this->getIssueIfAllowed($issueId);

        return $this->get('kreta_comment.form_handler.comment')->processForm(
            $this->get('request'), null, ['issue' => $issue]
        );
    }

    /**
     * Updates the comment of issue id and comment id given.
     *
     * @param string $issueId   The issue id
     * @param string $commentId The comment id
     *
     * @ApiDoc(statusCodes={200, 400, 403, 404})
     * @View(statusCode=200, serializerGroups={"comment"})
     *
     * @return \Kreta\Component\Comment\Model\Interfaces\CommentInterface
     */
    public function putCommentsAction($issueId, $commentId)
    {
        $comment = $this->getCommentIfAllowed($commentId, $issueId);

        return $this->get('kreta_comment.form_handler.comment')->processForm(
            $this->get('request'), $comment, ['method' => 'PUT', 'issue' => $comment->getIssue()]
        );
    }

    /**
     * Gets the comment if the current user is granted and if issue exists.
     *
     * @param string $commentId  The comment id
     * @param string $issueId    The issue id
     * @param string $issueGrant The issue grant
     *
     * @return \Kreta\Component\Comment\Model\Interfaces\CommentInterface
     */
    protected function getCommentIfAllowed($commentId, $issueId, $issueGrant = 'view')
    {
        $this->getIssueIfAllowed($issueId, $issueGrant);

        return $this->get('kreta_comment.repository.comment')->findOneBy(
            ['id' => $commentId, 'writtenBy' => $this->getUser()],
            false
        );
    }
}
