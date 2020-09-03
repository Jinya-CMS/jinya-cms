<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:40
 */

namespace Jinya\Entity\Form;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\HistoryEnabledEntity;
use function array_key_exists;
use function implode;

/**
 * @ORM\Entity
 * @ORM\Table(name="form_item", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="idx_form_item_position_form", columns={"position", "form_id"})
 * })
 */
class FormItem extends HistoryEnabledEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private ?int $id = -1;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(type="json")
     */
    private array $options = [];

    /**
     * @var array
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $spamFilter = null;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $label;

    /**
     * @ORM\Column(type="string")
     */
    private string $helpText;

    /**
     * @ORM\ManyToOne(inversedBy="items", targetEntity="Jinya\Entity\Form\Form", cascade={"persist"})
     */
    private Form $form;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $position;

    /**
     * @return array
     */
    public function getSpamFilter(): ?array
    {
        return $this->spamFilter;
    }

    public function setSpamFilter(array $spamFilter): void
    {
        $this->spamFilter = $spamFilter;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getHelpText(): string
    {
        return $this->helpText;
    }

    public function setHelpText(string $helpText): void
    {
        $this->helpText = $helpText;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
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
            'selectOptions' => $selectOptions,
            'spamFilter' => $this->spamFilter,
        ];
    }
}
