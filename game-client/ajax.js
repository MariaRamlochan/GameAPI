var ajaxObj;

//-----------GAME-----------

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
                        <th>Short Description</th>
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

//------------APP------------

async function getListOfApps(){
    //-- We'll implement an AJAX call using the Fetch Api
        let resourceUri = "http://localhost/GameAPI/game-server/apps";
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
            renderApps(data);
        }
} 

function renderApps(apps) {
    //-- Convert the plaintext representation of the data into in-memory.
        let rows = "";
        rows += `<thead>
                    <tr>   
                        <th>APP ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Developer</th>
                        <th>Downloads</th>
                        <th>Description</th>
                        <th>APP Url</th>
                        <th>Icon</th>
                    </tr>
                </thead>
                <tbody>`

        for (var item in apps) {
            var app = apps[item];
            rows += `<tr> 
                        <td>${app.app_id}</td>   
                        <td>${app.app_name}</td>
                        <td>${app.app_category}</td> 
                        <td>${app.app_developer}</td>
                        <td>${app.num_downloads}</td> 
                        <td>${app.app_description}</td> 
                        <td>${app.app_url}</td> 
                        <td><image src="${app.app_icon}" width="500%" height="auto"</td> 
                    </tr>`;
        }
        rows += `</tbody>`;
        let tableContainer = document.getElementById("dynamicTable");
        let itemsCountBadge = document.getElementById("appsBadgeNum");
        tableContainer.innerHTML = rows;
        itemsCountBadge.innerHTML = apps.length;
}

//----------STREAMER----------



//----------REVIEWS-----------