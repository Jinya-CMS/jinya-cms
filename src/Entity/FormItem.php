<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:40
 */

namespace Jinya\Entity;


use Doctrine\ORM\Mapping as ORM;
use function array_key_exists;
use function implode;

/**
 * @ORM\Entity
 * @ORM\Table(name="form_item", uniqueConstraints={@ORM\UniqueConstraint(name="idx_form_item_position_form", columns={"position", "form_id"})})
 */
class FormItem extends HistoryEnabledEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(type="json_array")
     * @var array
     */
    private $options;
    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $label;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $helpText;
    /**
     * @ORM\ManyToOne(inversedBy="items", targetEntity="Jinya\Entity\Form", cascade={"persist"})
     * @var Form
     */
    private $form;
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $position;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @param Form $form
     */
    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return $this->helpText;
    }

    /**
     * @param string $helpText
     */
    public function setHelpText(string $helpText): void
    {
        $this->helpText = $helpText;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        if (array_key_exists('choices', $this->options)) {
            $selectOptions = implode("\r\n", $this->options['choices']);
        } else {
            $selectOptions = '';
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'label' => $this->label,
            'helpText' => $this->helpText,
            'options' => $this->options,
            'required' => $this->options['required'],
            'selectOptions' => $selectOptions
        ];
    }
}