<?php
namespace Phosphorum\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phosphorum\Models\Categories;

class ProfilesForm extends Form
{

    public function initialize($entity = null, $options = null)
    {
        if (isset($options['edit']) && $options['edit']) {
            $id = new Hidden('id');
        } else {
            $id = new Text('id');
        }

        $this->add($id);

        $name = new Text('name', array(
            'placeholder' => 'Name'
        ));

        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'The name is required'
            ))
        ));

        $this->add($name);

        $description = new Text('description', array(
            'placeholder' => 'Description'
        ));

        $description->addValidators(array(
            new PresenceOf(array(
                'message' => 'The description is required'
            ))
        ));

        $this->add($description);

        $this->add(new Select('sub', Categories::find('id'), array(
            'using' => array(
                'name',// anciennement id        // avec name ici
                'name'// anciennement name         // et id ici je fais defiler 70 en sub du coup le sub recupere est le name avec l id 70
            ),
            'useEmpty' => true,
            'emptyText' => '...',
            'emptyValue' => ''
        )));

        $email = new Text('email', array(
            'placeholder' => 'Email'
        ));

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'The e-mail is required'
            )),
            new Email(array(
                'message' => 'The e-mail is not valid'
            ))
        ));

        $this->add($email);

        $this->add(new Select('active', array(
            'Y' => 'Yes',
            'N' => 'No'
        )));
    }
}
