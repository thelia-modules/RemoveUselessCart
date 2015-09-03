<?php

namespace RemoveUselessCart\Form;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ExecutionContextInterface;
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
                    'label' => $this->translator->trans('Remove older carts from this date', [],  'removeuselesscart'),
                    'required' => true,
                    'constraints' => [
                        new Constraints\NotBlank(),
                        new Constraints\Callback([
                            "methods" => [[ $this, "checkDate" ]],
                        ])
                    ]
                ]
            )
            ->add('remove_all', 'checkbox',
                [
                    'label' => $this->translator->trans('Remove even not empty carts', [], 'removeuselesscart')
                ]
            );
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
            $context->addViolation(
                $this->translator->trans(
                    "Date '%date' is invalid, please enter a valid date using %fmt format",
                    [
                        '%date' => $value,
                        '%fmt' => self::MOMENT_JS_DATE_FORMAT
                    ],
                    'removeuselesscart'
                )
            );
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
