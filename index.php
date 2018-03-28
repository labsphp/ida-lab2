<?php
/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 24.03.2018
 * Time: 23:07
 */

declare(strict_types = 1);
include_once 'HierarchialClustering.php';
include_once 'Node.php';

define("NUM_OF_CLUSTERS", 3);

//$dataSet = ["New meeting tomorrow file", "Corporate party tomorrow ", "Free sales party", "Free file for you",
//  "New greeting text", "Free file upload"];

/*$dataSet = [
"On Wednesday, central bankers also approved the widely expected quarter-point hike that puts the new benchmark funds rate at a target of 1.5 percent to 1.75 percent. It was the sixth rate increase since the policymaking Federal Open Market Committee began raising rates off near zero in December 2015.",
"Specifically, PG&E will replace the capacity with energy storage, energy efficiency and electric system upgrades. The utility will launch a request for offers this spring for providers of distributed energy resources. Roy Kuga, vice president of grid integration and innovation for PG&E, thanked the system operator's staff for its hard work reviewing PG&E's proposal.The Oakland Clean Energy Initiative represents an innovative, tailored portfolio of distributed clean energy resources combined with traditional transmission substation upgrades that meet the local reliability needs in this area of Oakland, enabling the retirement of the aging, jet fuel-powered plant",
"Trade war threat is now Wall Street's top economic fear, says. The market has shifted from a fear of a monetary policy misstep, tightening too aggressively, to a trade policy mistake, escalating into a trade war with China, Art Hogan, chief market strategist at B. Riley FBR, wrote in his response to the survey. The balance of risk for equities has moved from the Fed to the White House.",
"Our featured builders have created homes with social gathering spaces in mind and all the features and finishes that today’s home buyers could want. Nature surrounds our community and is blended into our amenities: winding trails, bike paths, swimming pool, a lake for paddling and fishing, and a well-equipped fitness center and more",
"Portobello under threat from Westway new homes plan say locals. Locals are divided over plans to build a covered market, arts venue and new homes under the Westway.",
"A huge new solar development being constructed in Virginia announced it will sell much of its energy to Microsoft. Pleinmont I and II will generate 500 MW when finished and will not only be Virginia’s largest solar project, but single-handedly double the amount of solar in the state. The project is owned by sPower.Microsoft will purchase 315 MW of that total, with the rest available for other entities.",
"Trade war threat is now Wall Street's top economic fear, says. Protectionism tops the list of worries on Wall Street, the  shows, far outpacing concerns over inflation, terrorism and even the Fed itself. President Donald Trump in recent weeks announced sweeping tariffs on steel and aluminum tariffs, and then exempted Canada and Mexico pending the outcome of talks on the North American Free Trade Agreement. The president has allowed for other exemptions, setting off a flurry of lobbying by countries and companies in the U.S. and abroad. The outcome of the exemption process is unclear so far."
];
*/
$dataSet = include("loadData.php");

$clustering = new HierarchialClustering($dataSet, NUM_OF_CLUSTERS);
$nodes = $clustering->cluster();
foreach ($nodes as $node) {
    echo 'CLUSTER<br>';
    $cluster = $clustering->bfs($node);
    foreach ($cluster as $sign => $element) {
        echo $cluster[$element] . $element->getWord() . '<br>';
    }
    echo '<hr>';
}

