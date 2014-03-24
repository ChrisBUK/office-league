<table class="table table-striped table-responsive c-league-table">
    <thead>
        <tr class="c-thead-main">
            <th colspan="11"><?php echo $objData->competition->name; ?></th>
        </tr>
        <tr class="c-thead-label">
            <th class='hidden-xs'>&nbsp;</th>
            <th class='t-align-center'>#</th>
            <th>Team</th>
            <th class='t-align-center'>P</th>
            <th class='t-align-center'>W</th>
            <th class='t-align-center'>D</th>
            <th class='t-align-center'>L</th>
            <th class='t-align-center hidden-xs'>F</th>
            <th class='t-align-center hidden-xs'>A</th>
            <th class='t-align-center hidden-xs'>GD</th>
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
                echo "<td class='t-align-center hidden-xs'>".$strIcon."</td>";
                echo "<td class='t-align-center'>".($intPos+1)."</td>";
                echo "<td class='t-nowrap'>".$objLine->teamName."</td>";
                echo "<td class='t-align-center'>".$objLine->gamesPlayed."</td>";
                echo "<td class='t-align-center'>".$objLine->gamesWon."</td>";
                echo "<td class='t-align-center'>".$objLine->gamesDrawn."</td>";
                echo "<td class='t-align-center'>".$objLine->gamesLost."</td>";
                echo "<td class='t-align-center hidden-xs'>".$objLine->scoreFor."</td>";
                echo "<td class='t-align-center hidden-xs'>".$objLine->scoreAgainst."</td>";
                echo "<td class='t-align-center hidden-xs'>".$objLine->scoreDifference."</td>";
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
