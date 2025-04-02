# Chocbar Docs

## Calling A Managers Func

Example Open Navigater Form For Player

`$player` - Player To Run The Func For

`$id = "openNavi"` - the id of the call

`$this->plugin->onInteract($player, $id ?? null);`



## Spawning An Npc For A Player

To Spawn An Npc Call spawnHubNPC on npcSystem

1. `$player` - Player To Spawn The NPC For
2. `$world` - World To Spawn The NPC In 
3. `new Vector3(0.52, 30, -37.44)` - Location Of The NPC In The World
4. `"Survival Mode"` - The Nametag On The NPC
5. `"survival"` - The Id Of The NPC When tapped this is used to identify the npc this must be binded to a call on interact otherwise nothing will be called on tapping it

`$this->npcSystem->spawnHubNPC($player, $world, new Vector3(0.52, 30, -37.44), "Survival Mode", "survival");`





