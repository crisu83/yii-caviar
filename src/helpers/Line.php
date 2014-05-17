<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\helpers;

class Line
{
    const RED = 'red';
    const GREEN = 'green';
    const YELLOW = 'yellow';
    const BLUE = 'blue';
    const MAGENTA = 'magenta';
    const CYAN = 'cyan';
    const WHITE = 'white';
    const GRAY = 'gray';

    /**
     * @var string contents of this line.
     */
    protected $content = '';

    /**
     * @var array list of colors available.
     */
    protected static $colors = array(
        Line::RED => 31,
        Line::GREEN => 32,
        Line::YELLOW => 33,
        Line::BLUE => 34,
        Line::MAGENTA => 35,
        Line::CYAN => 36,
        Line::WHITE => 37,
        Line::GRAY => 38,
    );

    /**
     * Creates a new line.
     *
     * @param string $string initial content.
     * @param int|null $color text color.
     * @param bool $bold whether to use bold text.
     */
    function __construct($string = '', $color = null, $bold = false)
    {
        $this->text($string, $color, $bold);
    }

    /**
     * Adds texts to this line.
     *
     * @param string $string text to add.
     * @param int|null $color text color.
     * @param bool $bold whether to use bold text.
     * @return Line this line.
     */
    public function text($string, $color = null, $bold = false)
    {
        if ($color !== null) {
            $this->colorize($color, (int) $bold);
        }

        if (!empty($string)) {
            $this->content .= $string . ' ';
        }

        $this->normalize();

        return $this;
    }

    /**
     * Indents this line by the given amount.
     *
     * @param int $amount amount to indent (defaults to 1).
     * @return Line this line.
     */
    public function indent($amount = 1)
    {
        $this->content .= str_repeat(' ', $amount);

        return $this;
    }

    /**
     * Moves the caret to the given index.
     *
     * @param int $index new index for caret.
     * @return Line this line.
     */
    public function to($index)
    {
        return $this->indent(($amount = $index - $this->length()) > 0 ? $amount : 0);
    }

    /**
     * Ends this line and returns it.
     *
     * @return string line contents.
     */
    public function end()
    {
        $content = $this->content;

        $this->content = '';

        return $content;
    }

    /**
     * Adds the given amount of line breaks to this line.
     *
     * @param int $amount amount of line breaks.
     * @return string line contents.
     */
    public function nl($amount = 1)
    {
        $this->content .= str_repeat(PHP_EOL, $amount);

        return $this->end();
    }

    /**
     * Adds a color code to this line.
     *
     * @param int $color color code.
     * @param int $bold whether to use bold text.
     * @throws Exception if the color is invalid.
     */
    protected function colorize($color, $bold = 0)
    {
        if (!isset(self::$colors[$color])) {
            throw new Exception("Unknown color '$color'.");
        }

        $code = self::$colors[$color];
        $this->content .= "\033[{$bold};{$code}m";
    }

    /**
     * Normalizes this code by adding a reset code to this line.
     */
    protected function normalize()
    {
        $this->content .= "\033[0m";
    }

    /**
     * Returns the length of this line.
     *
     * @return int line length.
     */
    protected function length()
    {
        return strlen(preg_replace('/\033\[[\d;?]*\w/', '', $this->content));
    }

    /**
     * Creates a new line and returns it.
     *
     * @param string $string initial content.
     * @param int|null $color text color.
     * @param bool $bold whether to use bold text.
     * @return Line a new line.
     */
    public static function begin($string = '', $color = null, $bold = false)
    {
        return new Line($string, $color, $bold);
    }
}