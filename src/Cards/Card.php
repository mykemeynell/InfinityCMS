<?php

namespace Infinity\Cards;

use Illuminate\View\View;

abstract class Card
{
    public static string $width = '1/3';
    public static string $title;
    public string $titleButtonLabel = '';
    public string $titleButtonLink = '';
    public static array $groups = [];
    private array $cardData = [];

    /**
     * @return string
     */
    abstract public function view(): string;

    /**
     * Get the view.
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function render(): View
    {
        return view('infinity::cards.default', [
            'contentView' => view($this->view(), $this->cardData),
            'width' => static::$width,
            'title' => $this->getTitle(),
            'hasTitleButton' => $this->hasTitleButton(),
            'titleButtonLabel' => $this->titleButtonLabel,
            'titleButtonLink' => $this->titleButtonLink,
        ]);
    }

    /**
     * Get the title.
     *
     * @throws \Exception
     */
    private function getTitle(): string
    {
        if(empty(static::$title)) {
            throw new \Exception(sprintf("Set title on %s", static::class));
        }

        return static::$title;
    }

    /**
     * Set the title button.
     *
     * @param string $label
     * @param string $location
     *
     * @return void
     */
    protected function setTitleButton(string $label, string $location): void
    {
        $this->titleButtonLabel = $label;
        $this->titleButtonLink = $location;
    }

    /**
     * Test if the card has a title button.
     *
     * @return bool
     */
    public function hasTitleButton(): bool
    {
        return !empty($this->titleButtonLink) && !empty($this->titleButtonLabel);
    }

    /**
     * Add data that can be output with the card contents.
     *
     * @param string $key
     * @param        $data
     *
     * @return void
     */
    protected function addCardData(string $key, $data): void
    {
        $this->cardData[$key] = $data;
    }
}
