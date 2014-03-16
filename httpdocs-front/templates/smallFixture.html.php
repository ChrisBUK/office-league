<div class="fixtureTable">
    <table>
        <thead>
            <tr class="tableName">
                <th colspan="7">Fixtures &amp; Results</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($objData as $intRound=>$arrFixtures)
                {
                    echo "<tr>";
                    echo "<td colspan='7' class='roundName'>Round ".($intRound)."</td>";
                    echo "</tr>";

                    foreach ($arrFixtures as $objLine)
                    {
                        echo "<tr>";
                        echo "<td class='fixtureId alignLeft'>#".$objLine->fixtureId."</td>";
                        echo "<td class='teamName alignRight'>".$objLine->homeTeamName."</td>";

                        if ($objLine->isPlayed == 1)
                        {
                            echo "<td class='alignRight'>".$objLine->homeTeamScore."</td>";
                            echo "<td class='alignCenter'>&ndash;</td>";
                            echo "<td class='alignLeft'>".$objLine->awayTeamScore."</td>";                    
                        } else {
                            echo "<td class='alignRight'>&nbsp;</td>";
                            echo "<td class='alignCenter'><a target='_blank' href='http://YOUR_HOST_HERE/update/result?fixtureId=".$objLine->fixtureId."'>v</a></td>";
                            echo "<td class='alignLeft'>&nbsp;</td>";                                        
                        }

                        echo "<td class='teamName alignLeft'>".$objLine->awayTeamName."</td>";
                        echo "<td class='compName alignRight'>".$objLine->competitionName."</td>";

                        echo "</tr>";
                    }        
                }
            ?>    
        </tbody>
    </table>
</div>