<div class="c-fixture-table">
    <table class="table table-striped">
        <thead>
            <tr class="c-thead-main">
                <th colspan="7">Fixtures &amp; Results</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($objData as $intRound=>$arrFixtures)
                {
                    echo "<tr>";
                    echo "<td colspan='7' class='c-thead-label'>Round ".($intRound)."</td>";
                    echo "</tr>";

                    foreach ($arrFixtures as $objLine)
                    {
                        echo "<tr>";
                        echo "<td class='c-competition-name t-align-left'>".$objLine->competitionName."</td>";
                        echo "<td class='c-team-name t-align-right'>".$objLine->homeTeamName."</td>";

                        if ($objLine->isPlayed == 1)
                        {
                            $strIcon = 'ok';
                            echo "<td class='t-align-right'>".$objLine->homeTeamScore."</td>";
                            echo "<td class='t-align-center'>&ndash;</td>";
                            echo "<td class='t-align-left'>".$objLine->awayTeamScore."</td>";                    
                        } else {
                            $strIcon = 'remove';
                            echo "<td class='t-align-right'>&nbsp;</td>";
                            echo "<td class='t-align-center'><a target='_blank' href='".Config::API_HOST."/update/result?fixtureId=".$objLine->fixtureId."'>v</a></td>";
                            echo "<td class='t-align-left'>&nbsp;</td>";                                        
                        }

                        echo "<td class='c-team-name t-align-left'>".$objLine->awayTeamName."</td>";
                        echo "<td class='t-align-right t-color-light'><span class='glyphicon glyphicon-".$strIcon."'></span>";

                        echo "</tr>";
                    }        
                }
            ?>    
        </tbody>
    </table>
</div>