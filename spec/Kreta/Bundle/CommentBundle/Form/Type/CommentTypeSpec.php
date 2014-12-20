<?php

/**
 * This file belongs to Kreta.
 * The source code of application includes a LICENSE file
 * with all information about license.
 *
 * @author benatespina <benatespina@gmail.com>
 * @author gorkalaucirica <gorka.lauzirika@gmail.com>
 */

namespace spec\Kreta\Bundle\CommentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;

/**
 * Class CommentTypeSpec.
 *
 * @package spec\Kreta\Bundle\CommentBundle\Form\Type
 */
class CommentTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kreta\Bundle\CommentBundle\Form\Type\CommentType');
    }

    function it_extends_form_abstract_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_a_form(FormBuilder $builder)
    {
        $builder->add('description', 'textarea', [
            'required' => true,
            'label'    => false
        ])->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_gets_name()
    {
        $this->getName()->shouldReturn('kreta_comment_comment_type');
    }
}
