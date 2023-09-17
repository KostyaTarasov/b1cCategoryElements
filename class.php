<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Main\Entity;

class CategoriesAndElementsComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule("iblock")) {
            ShowError("IBlock module is not installed");
            return;
        }

        $cache = new CPHPCache;
        $cacheId = "mycomponent_" . $this->arParams["IBLOCK_ID"];
        $cachePath = "/mycomponent_cache/";

        if ($this->StartResultCache(false, [$this->arParams, $cachePath])) {
            $this->arResult["SECTIONS"] = $this->getSectionsWithElements($this->arParams["IBLOCK_ID"]);
            $this->IncludeComponentTemplate();
            $this->EndResultCache();
        }

        $this->setResultCacheKeys(["SECTIONS"]);
    }

    private function getSectionsWithElements($iblockId)
    {
        $sections = [];
        $elementProperty = [];

        $dbSections = Iblock\SectionTable::getList([
            "select" => ["ID", "NAME"],
            "filter" => ["IBLOCK_ID" => $iblockId],
        ]);

        while ($section = $dbSections->fetch()) {
            $elementIds = $this->getSectionElementIds($section["ID"], $iblockId);

            if (!empty($elementIds)) {
                $elementProperty = $this->getElementsTags($elementIds, $iblockId);
                $sections[] = [
                    "ID" => $section["ID"],
                    "NAME" => $section["NAME"],
                    "ELEMENTS" => $elementProperty,
                ];
            }
        }

        return $sections;
    }

    private function getSectionElementIds($sectionId, $iblockId)
    {
        $elementIds = [];
        $dbElement = Iblock\SectionElementTable::getList([
            "select" => ["IBLOCK_ELEMENT_ID"],
            "filter" => [
                "SECTION_ID" => $sectionId,
                "IBLOCK_ID" => $iblockId,
            ],
        ]);

        while ($element = $dbElement->fetch()) {
            $elementIds[] = $element["IBLOCK_ELEMENT_ID"];
        }

        return $elementIds;
    }

    private function getElementsTags($elementIds, $iblockId)
    {
        $elements = [];
        $dbElements = Iblock\ElementTable::getList([
            "select" => ["ID", "NAME", "PROPERTY_TAGS"],
            "filter" => [
                "ID" => $elementIds,
                "IBLOCK_ID" => $iblockId,
            ],
        ]);

        while ($element = $dbElements->fetch()) {
            $tags = [];
            if (!empty($element["PROPERTY_TAGS_VALUE"])) {
                $tags = explode(", ", $element["PROPERTY_TAGS_VALUE"]);
            }
            $elements[] = [
                "ID" => $element["ID"],
                "NAME" => $element["NAME"],
                "TAGS" => $tags,
            ];
        }

        return $elements;
    }
}
