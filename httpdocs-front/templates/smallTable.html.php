<div class="leagueTable">
    <table>
        <thead>
            <tr class="tableName">
                <th colspan="11"><?php echo $objData->competition->name; ?></th>
            </tr>
            <tr class="tableFieldHeader">
                <th>&nbsp;</th>
                <th class='alignCenter'>#</th>
                <th>Team</th>
                <th class='alignCenter'>Played</th>
                <th class='alignCenter'>Won</th>
                <th class='alignCenter'>Drawn</th>
                <th class='alignCenter'>Lost</th>
                <th class='alignCenter'>For</th>
                <th class='alignCenter'>Against</th>
                <th class='alignCenter'>GD</th>
                <th class='alignCenter'>Pts</th>
            </tr>
        </thead>
        <tbody>
            <colgroup>
                <col span="3" class="cgLeft" />
                <col span="4" class="cgGames" />
                <col span="3" class="cgGoals" />
                <col span="1" class="cgPoints" />
            </colgroup>
            <?php
                foreach ($objData->table as $intPos=>$objLine)
                {
                    switch (true)
                    {
                        case ($objLine->currentPosition < $objLine->previousPosition):
                            $strIcon = "+";
                            break;
                        case ($objLine->currentPosition > $objLine->previousPosition):
                            $strIcon = "-";
                            break;
                        default:
                            $strIcon = "=";
                            break;
                    }
                    
                    echo "<tr>";
                    echo "<td class='alignCenter'>".$strIcon."</td>";
                    echo "<td class='alignCenter'>".($intPos+1)."</td>";
                    echo "<td>".$objLine->teamName."</td>";
                    echo "<td class='alignCenter'>".$objLine->gamesPlayed."</td>";
                    echo "<td class='alignCenter'>".$objLine->gamesWon."</td>";
                    echo "<td class='alignCenter'>".$objLine->gamesDrawn."</td>";
                    echo "<td class='alignCenter'>".$objLine->gamesLost."</td>";
                    echo "<td class='alignCenter'>".$objLine->scoreFor."</td>";
                    echo "<td class='alignCenter'>".$objLine->scoreAgainst."</td>";
                    echo "<td class='alignCenter'>".$objLine->scoreDifference."</td>";
                    echo "<td class='alignCenter'>".$objLine->pointsTotal."</td>";
                    echo "</tr>";            
                }
            ?>    
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11"><?php echo $objData->competition->rules; ?></td>
            </tr>
        </tfoot>
    </table>
</div>