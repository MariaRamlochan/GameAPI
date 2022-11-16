var ajaxObj;

async function getListOfGames(){
    //-- We'll implement an AJAX call using the Fetch Api
        let resourceUri = "http://localhost/GameAPI/game-server/games";
        const options = {
            method: "GET",
            
            headers: {
                "Accept": "application/json",
                
            }
        };
    
        //-- 1) Create a Request object
        const request = new Request(resourceUri, options);
        //-- 2) Fetch the Response
        const response = await fetch(request);
        //-- 3) Check if the request was ok 
        if (response.ok) {
            //-- 4) We can parse the data
            data = await response.json();
            //console.log(data);
            renderGames(data);
        }
} 

function renderGames(games) {
    //-- Convert the plaintext representation of the data into in-memory.
        let rows = "";
        rows += `<thead>
                    <tr>   
                        <th>Game ID</th>
                        <th>Title</th>
                    </tr>
                </thead>
                <tbody>`

        for (var item in games) {
            var game = games[item];
            rows += `<tr> 
                        <td>${game.game_id}</td>   
                        <td>${game.title}</td>   
                    </tr>`;
        }
        rows += `</tbody>`;
        let tableContainer = document.getElementById("dynamicTable");
        let itemsCountBadge = document.getElementById("gamesBadgeNum");
        tableContainer.innerHTML = rows;
        itemsCountBadge.innerHTML = games.length;
}
