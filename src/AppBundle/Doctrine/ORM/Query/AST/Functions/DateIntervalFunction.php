<?php
/**
 * Date: 30-Aug-16
 * Time: 16:42
 */

namespace AppBundle\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Herrera\DateInterval\DateInterval;

/**
 * "DATE_INTERVAL" "(" StringPrimary ")"
 */
class DateIntervalFunction extends FunctionNode
{
    /**
     * The extracted date interval specification.
     *
     * @var Literal
     */
    private $intervalSpec;

    /**
     * @override
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return (new \DateInterval($this->intervalSpec->value))->format('%H:%I:%S');
    }

    /**
     * @override
     * @param Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->intervalSpec = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}