var ajaxObj;



// -----------------------DISPLAY THE LIST OF ALL GAMES-----------------------

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
                        <th>Short Descrip</th>
                        <th>Game Url</th>
                        <th>Release Date</th>
                        <th>Genre</th>
                        <th>Platform</th>
                        <th>Publisher</th>
                        <th>Developer</th>
                        <th>Thumbnail</th>
                    </tr>
                </thead>
                <tbody>`

        for (var item in games) {
            var game = games[item];
            rows += `<tr> 
                        <td>${game.game_id}</td>   
                        <td>${game.title}</td>
                        <td>${game.short_description}</td>   
                        <td>${game.game_url}</td>
                        <td>${game.release_date}</td>
                        <td>${game.genre}</td>
                        <td>${game.platform}</td>
                        <td>${game.publisher}</td>
                        <td>${game.developer}</td>
                        <td><image src="${game.thumbnail}"></td>
                    </tr>`;
        }
        rows += `</tbody>`;
        let tableContainer = document.getElementById("dynamicTable");
        let itemsCountBadge = document.getElementById("gamesBadgeNum");
        tableContainer.innerHTML = rows;
        itemsCountBadge.innerHTML = games.length;
}


// -----------------------UPDATE THE ENTRIE(S) OF A GAME-----------------------

async function updateGameEntries(gameID){
    //-- We'll implement an AJAX call using the Fetch Api
        let resourceUri = "http://localhost/GameAPI/game-server/games/"+gameID+"";
        const options = {
            method: "PUT",
            
            headers: {
                "Accept": "application/json",
            },
            body:'{"FOR LATER"}'
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
            parseGameByID(data, gameID);
        }
} 

function parseGameByID(games, gameID) {
    //-- Convert the plaintext representation of the data into in-memory.
        let rows = "";
        rows += `<thead>
                    <tr>   
                        <th>Game ID</th>
                        <th>Title</th>
                        <th>Short Descrip</th>
                        <th>Game Url</th>
                        <th>Release Date</th>
                        <th>Genre</th>
                        <th>Platform</th>
                        <th>Publisher</th>
                        <th>Developer</th>
                        <th>Thumbnail</th>
                    </tr>
                </thead>
                <tbody>`

        for (var item in games) {
            var game = games[item];
            rows += `<tr> 
                        <td>${game.game_id}</td>   
                        <td>${game.title}</td>
                        <td>${game.short_description}</td>   
                        <td>${game.game_url}</td>
                        <td>${game.release_date}</td>
                        <td>${game.genre}</td>
                        <td>${game.platform}</td>
                        <td>${game.publisher}</td>
                        <td>${game.developer}</td>
                        <td><image src="${game.thumbnail}"></td>
                    </tr>`;
        }
        rows += `</tbody>`;
        let tableContainer = document.getElementById("dynamicTable");
        let itemsCountBadge = document.getElementById("gamesBadgeNum");
        tableContainer.innerHTML = rows;
        itemsCountBadge.innerHTML = games.length;
}



