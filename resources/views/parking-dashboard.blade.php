<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	<title>Parking Tracker - Guard Dashboard</title> 
    <style> 
        body { font-family: Arial, sans-serif; margin: 20px; background: #f2f2f2; } 
	    h1 { margin-bottom: 5px; } 
        p { margin-top: 0; color: #555; } 
        .container { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; } 
	    .card { background: #fff; border: 1px solid #ddd; padding: 12px; border-radius: 6px; } 
        label { display: block; margin-top: 8px; font-size: 14px; } 
        input,  select,  button { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; } 
	    button { cursor: pointer; background: #2f6fed; border: 0; color: white; border-radius: 4px; } 
        button:hover { background: #1f54b7; } 
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; } 
	    .output { white-space: pre-wrap; background: #111; color: #0f0; padding: 10px; height: 250px; overflow: auto; font-size: 12px; } 
        .small { font-size: 12px; color: #666; } 
    </style> 
</head> 
<body> 
    <h1>Parking Tracker Dashboard</h1> 
	<p>Simple guard tool for testing. No auth, no polish.</p> 

    <div class="container"> 
	    <div class="card"> 
            <h3>System Actions</h3> 
            <button id="seedBtn">Seed Sections</button> 
	        <button id="loadSectionsBtn" style="margin-top:8px;">Load All Sections</button> 
            <button id="loadAvailableBtn" style="margin-top:8px;">Load Available Sections</button> 
            <button id="loadActiveBtn" style="margin-top:8px;">Load Active Tickets</button> 
	        <p class="small">Seed first if this is your first run.</p> 
        </div> 

	    <div class="card"> 
            <h3>Check In Driver</h3> 
            <label>Plate Number</label> 
	        <input id="plateNumber" type="text" placeholder="ABC-1234"> 
            <div class="row"> 
                <div> 
	                <label>Floor</label> 
                    <select id="floor"> 
                        <option value="1">1</option> 
	                    <option value="2">2</option> 
                    </select> 
                </div> 
	            <div> 
                    <label>Section</label> 
                    <select id="sectionCode"> 
	                    <option value="A">A</option> 
                        <option value="B">B</option> 
                    </select> 
	            </div> 
            </div> 
            <button id="checkInBtn" style="margin-top:10px;">Check In</button> 
	    </div> 

        <div class="card"> 
	        <h3>Check Out Driver</h3> 
            <label>Ticket ID</label> 
            <input id="ticketId" type="number" placeholder="1"> 
	        <button id="checkOutBtn" style="margin-top:10px;">Check Out</button> 
        </div> 

	    <div class="card"> 
            <h3>Raw Response</h3> 
            <div id="output" class="output">Ready</div> 
	    </div> 
    </div> 

	<script> 
        const output = document.getElementById('output'); 

	    function print(data) { 
            if (typeof data === 'string') { 
                output.textContent = data; 
	            return; 
            } 
            output.textContent  =  JSON.stringify(data, null, 2); 
	    } 

        async function apiGet(url) { 
	        const res = await fetch(url); 
            const data  =  await res.json(); 
            print(data); 
	        return data; 
        } 

	    async function apiPost(url, payload) { 
            const res = await fetch(url,  { 
                method: 'POST', 
	            headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
	            }, 
                body: JSON.stringify(payload), 
            }); 

            const data  =  await res.json(); 
            print(data); 
	        return data; 
        } 

	    document.getElementById('seedBtn').addEventListener('click',  async () => { 
            await apiPost('/api/sections/seed', {}); 
        }); 

        document.getElementById('loadSectionsBtn').addEventListener('click', async () => { 
            await apiGet('/api/sections'); 
	    }); 

        document.getElementById('loadAvailableBtn').addEventListener('click', async () => { 
	        await apiGet('/api/sections/available'); 
        }); 

	    document.getElementById('loadActiveBtn').addEventListener('click', async () => { 
            await apiGet('/api/tickets/active'); 
        }); 

        document.getElementById('checkInBtn').addEventListener('click', async () => { 

            const payload = { 
	            plate_number: document.getElementById('plateNumber').value, 
                floor: document.getElementById('floor').value, 
                section_code: document.getElementById('sectionCode').value, 
	        }; 
            await apiPost('/api/check-in', payload); 
        }); 

        document.getElementById('checkOutBtn').addEventListener('click', async () => { 
            const payload = { 
	            ticket_id: document.getElementById('ticketId').value, 
            }; 
            await apiPost('/api/check-out', payload); 
	    }); 
    </script> 
</body> 
</html> 
