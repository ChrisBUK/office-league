<div class="c-league-table">
    <table class="table table-striped">
        <thead>
            <tr class="c-thead-main">
                <th colspan="11"><?php echo $objData->competition->name; ?></th>
            </tr>
            <tr class="c-thead-label">
                <th>&nbsp;</th>
                <th class='t-align-center'>#</th>
                <th>Team</th>
                <th class='t-align-center'>P</th>
                <th class='t-align-center'>W</th>
                <th class='t-align-center'>D</th>
                <th class='t-align-center'>L</th>
                <th class='t-align-center'>F</th>
                <th class='t-align-center'>A</th>
                <th class='t-align-center'>GD</th>
                <th class='t-align-center'>Pts</th>
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
                            $strIcon = '<span class="glyphicon glyphicon-chevron-up t-color-up"></span>';
                            break;
                        case ($objLine->currentPosition > $objLine->previousPosition):
                            $strIcon = '<span class="glyphicon glyphicon-chevron-down t-color-down"></span>';
                            break;
                        default:
                            $strIcon = '';                           
                            break;
                    }
                    
                    echo "<tr>";
                    echo "<td class='t-align-center'>".$strIcon."</td>";
                    echo "<td class='t-align-center'>".($intPos+1)."</td>";
                    echo "<td>".$objLine->teamName."</td>";
                    echo "<td class='t-align-center'>".$objLine->gamesPlayed."</td>";
                    echo "<td class='t-align-center'>".$objLine->gamesWon."</td>";
                    echo "<td class='t-align-center'>".$objLine->gamesDrawn."</td>";
                    echo "<td class='t-align-center'>".$objLine->gamesLost."</td>";
                    echo "<td class='t-align-center'>".$objLine->scoreFor."</td>";
                    echo "<td class='t-align-center'>".$objLine->scoreAgainst."</td>";
                    echo "<td class='t-align-center'>".$objLine->scoreDifference."</td>";
                    echo "<td class='t-align-center'>".$objLine->pointsTotal."</td>";
                    echo "</tr>";            
                }
            ?>    
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11" class="t-align-right"><?php echo $objData->competition->rules; ?></td>
            </tr>
        </tfoot>
    </table>
</div>