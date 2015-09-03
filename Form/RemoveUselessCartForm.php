<?php

namespace RemoveUselessCart\Form;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 * Class RemoveUselessCartForm
 * @package RemoveUselessCart\Form
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class RemoveUselessCartForm extends BaseForm
{
    // The date format for the start date
    const PHP_DATE_FORMAT = "Y-m-d H:i:s";
    const MOMENT_JS_DATE_FORMAT = "YYYY-MM-DD HH:mm:ss";

    /**
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('start_date', 'text',
                [
                    'label' => 'Remove older carts from this date',
                    'required' => true,
                    'constraints' => [
                        new Constraints\NotBlank(),
                        new Constraints\Callback([
                            "methods" => [[ $this, "checkDate" ]],
                        ])
                    ]
                ]
            )
            ->add('remove_all', 'checkbox', ['label' => 'Remove even not empty carts']);
    }

    /**
     * Validate a date entered with the current edition Language date format.
     *
     * @param string                    $value
     * @param ExecutionContextInterface $context
     */
    public function checkDate($value, ExecutionContextInterface $context)
    {
        $format = self::PHP_DATE_FORMAT;

        if (! empty($value) && false === \DateTime::createFromFormat($format, $value)) {
            $context->addViolation(Translator::getInstance()->trans("Date '%date' is invalid, please enter a valid date using %fmt format", [
                '%fmt' => self::MOMENT_JS_DATE_FORMAT,
                '%date' => $value
            ]));
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'removeuselesscart_form';
    }
}
