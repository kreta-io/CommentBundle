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

namespace Kreta\Bundle\CommentBundle\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Kreta\Bundle\CoreBundle\Behat\Context\DefaultContext;
use Kreta\Component\Comment\Model\Comment;
use Kreta\Component\Comment\Model\CommentId;

/**
 * Comment Behat context class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class CommentContext extends DefaultContext
{
    /**
     * Populates the database with comments.
     *
     * @param \Behat\Gherkin\Node\TableNode $comments The comments
     *
     * @Given /^the following comments exist:$/
     */
    public function theFollowingCommentsExist(TableNode $comments)
    {
        foreach ($comments as $commentData) {
            $issue = $this->get('kreta_issue.repository.issue')->findOneBy(['title' => $commentData['issue']], false);
            $user = $this->get('kreta_user.repository.user')->findOneBy(['email' => $commentData['user']], false);

            $comment = Comment::publish(new CommentId(), $commentData['content'], $user, $issue);
            if (isset($commentData['updatedOn'])) {
                $this->setField($comment, 'updatedOn', new \DateTime($commentData['updatedOn']));
            }
            if (isset($commentData['createdOn'])) {
                $this->setField($comment, 'createdOn', new \DateTime($commentData['createdOn']));
            }
            if (isset($commentData['id'])) {
                $this->setId($comment, $commentData['id']);
            }

            $this->get('kreta_comment.repository.comment')->persist($comment);
        }
    }
}
