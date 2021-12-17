<?php
namespace EfTech\ContactListTest;

/** Набор утилит для тестирования приложения
 *
 */
class TestUtils
{
    /** Вычисляет расскхождение массивов с доп проверкой индекса. Поддержка многомерных массивов
     * @param array $a1
     * @param array $a2
     * @return array
     */
   public static function arrayDiffAssocRecursive(array $a1,array $a2):array
    {
        $result = [];
        foreach ($a1 as $k1 => $v1) {
            if(false === array_key_exists($k1, $a2)){
                $result[$k1] = $v1;
                continue;
            }
            if(is_iterable($v1) && is_iterable($a2[$k1])) {
                $resultCheck = self::arrayDiffAssocRecursive($v1,$a2[$k1]);
                if (count($resultCheck) > 0 ) {
                    $result[$k1] = $resultCheck;
                }
                continue;
            }
            if ($v1 !== $a2[$k1]) {
                $result[$k1] = $v1;
            }
        }
        return $result;
    }

}