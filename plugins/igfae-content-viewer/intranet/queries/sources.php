<?php


function getSources()
{
    $sourcesInclude[] = IGFAE_INTRANET_SOURCE_SCOPUS;
    $sourcesInclude[] = IGFAE_INTRANET_SOURCE_WOS;
    $sourcesInclude[] = IGFAE_INTRANET_SOURCE_INSPIRE;
    $paramsPlaceholders = array_fill(0, count($sourcesInclude), '%d');
    $placeholdersSources = implode(',', $paramsPlaceholders);
    $params = array_merge([], $sourcesInclude);
    $sql = "select s.SourceID, s.SourceName 
        from Source s 
        where s.SourceID IN ({$placeholdersSources})
        order by s.SourceName ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new Source(
            $row->SourceID,
            $row->SourceName
        );
    }

    return $result;
}