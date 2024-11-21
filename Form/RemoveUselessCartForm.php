<?php

namespace RemoveUselessCart\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
    public const PHP_DATE_FORMAT = "Y-m-d H:i:s";
    public const MOMENT_JS_DATE_FORMAT = "YYYY-MM-DD HH:mm:ss";

    /**
     * @return void
     */
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add('start_date', TextareaType::class,
                [
                    'label' => Translator::getInstance()->trans('Remove older carts from this date', [],  'removeuselesscart'),
                    'required' => true,
                    'constraints' => [
                        new Constraints\NotBlank(),
                        new Constraints\Callback([
                            "callback" => [$this, "checkDate"],
                        ])
                    ]
                ]
            )
            ->add('remove_all', CheckboxType::class,
                [
                    'label' => Translator::getInstance()->trans('Remove even not empty carts', [], 'removeuselesscart')
                ]
            );
    }

    /**
     * Validate a date entered with the current edition Language date format.
     *
     * @param string $value
     * @param ExecutionContextInterface $context
     */
    public function checkDate(string $value, ExecutionContextInterface $context): void
    {
        if (! empty($value) && false === \DateTime::createFromFormat(self::PHP_DATE_FORMAT, $value)) {
            $context->addViolation(
                Translator::getInstance()->trans(
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
    public static function getName(): string
    {
        return 'removeuselesscart_form';
    }
}
