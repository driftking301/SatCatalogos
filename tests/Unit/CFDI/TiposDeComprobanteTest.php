<?php

declare(strict_types=1);

namespace PhpCfdi\SatCatalogos\Tests\Unit\CFDI;

use PhpCfdi\SatCatalogos\CFDI\TiposDeComprobante;
use PHPUnit\Framework\TestCase;

class TiposDeComprobanteTest extends TestCase
{
    protected $validRow = [
        'id' => 'I',
        'texto' => 'Ingreso',
        'valor_maximo' => '999999999999999999.999999',
        'vigencia_desde' => '2017-07-29',
        'vigencia_hasta' => 0,
    ];

    public function testCreate()
    {
        $tiposDeComprobante = new TiposDeComprobante();
        $created = $tiposDeComprobante->create($this->validRow);

        $this->assertSame($created->id(), $this->validRow['id']);
        $this->assertSame($created->texto(), $this->validRow['texto']);
        $this->assertSame($created->valorMaximo(), $this->validRow['valor_maximo']);
        $this->assertSame($created->vigenteDesde(), strtotime($this->validRow['vigencia_desde']));
        $this->assertSame($created->vigenteHasta(), 0);
    }
}
