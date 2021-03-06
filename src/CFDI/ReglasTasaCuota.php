<?php

declare(strict_types=1);

namespace PhpCfdi\SatCatalogos\CFDI;

use PhpCfdi\SatCatalogos\Exceptions\SatCatalogosLogicException;
use PhpCfdi\SatCatalogos\Repository;
use PhpCfdi\SatCatalogos\WithRepositoryInterface;
use PhpCfdi\SatCatalogos\WithRepositoryTrait;

class ReglasTasaCuota implements WithRepositoryInterface
{
    use WithRepositoryTrait;

    const FACTOR_TASA = 'Tasa';

    const FACTOR_CUOTA = 'Cuota';

    const IMPUESTO_IEPS = 'IEPS';

    const IMPUESTO_IVA = 'IVA';

    const IMPUESTO_ISR = 'ISR';

    const USO_TRASLADO = 'traslado';

    const USO_RETENCION = 'retencion';

    /**
     * @param string $impuesto
     * @param string $factor
     * @param string $uso
     * @return ReglaTasaCuota[]
     */
    public function obtainRules(string $impuesto, string $factor, string $uso): array
    {
        if (self::USO_TRASLADO !== $uso && self::USO_RETENCION !== $uso) {
            throw new SatCatalogosLogicException('El campo uso no tiene uno de los valores permitidos');
        }
        $filters = [
            'impuesto' => $impuesto,
            'factor' => $factor,
            $uso => true,
        ];

        /** @var ReglaTasaCuota[] $rules */
        $rules = array_map(
            [$this, 'createRule'],
            $this->repository()->queryRowsByFields(Repository::CFDI_REGLAS_TASA_CUOTA, $filters)
        );

        return $rules;
    }

    /**
     * @param string $impuesto
     * @param string $factor
     * @param string $uso
     * @param string $valor
     * @return ReglaTasaCuota|null
     */
    public function findMatchingRule(string $impuesto, string $factor, string $uso, string $valor)
    {
        $rules = $this->obtainRules($impuesto, $factor, $uso);

        foreach ($rules as $rule) {
            if ($rule->valorIsValid($valor)) {
                return $rule;
            }
        }

        return null;
    }

    public function hasMatchingRule(string $impuesto, string $factor, string $uso, string $valor): bool
    {
        return (null !== $this->findMatchingRule($impuesto, $factor, $uso, $valor));
    }

    public function createRule(array $data): ReglaTasaCuota
    {
        return new ReglaTasaCuota(
            $data['tipo'],
            $data['impuesto'],
            $data['factor'],
            (bool) $data['traslado'],
            (bool) $data['retencion'],
            $data['minimo'],
            $data['valor'],
            ($data['vigencia_desde']) ? strtotime($data['vigencia_desde']) : 0,
            ($data['vigencia_hasta']) ? strtotime($data['vigencia_hasta']) : 0
        );
    }
}
