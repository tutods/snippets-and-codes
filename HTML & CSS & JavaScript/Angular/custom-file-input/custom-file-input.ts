/**
* Imports and component selector, templateUrl and styleUrls
*/	

export class MyComponent implements OnInit {
	
	/**
	* Other methods of your component (like constructor, OnInit, ...)
	*/
	
	/**
	* Method to run when have changes on input
	*/	
	showFileName(): void {
        const legend = document.getElementById('legend') as HTMLInputElement;
        const input = document.getElementById('file') as HTMLInputElement;

        let fileName = input.value;
        fileName = fileName.split('\\').slice(-1).pop();

        //  Add ... if name have more than 15 chars
        if (fileName.length >= 15) {
            const extension = fileName.split('.').slice(-1).pop();
            fileName = `${fileName.substr(0, 15)}(...).${extension}`;
        }

        legend.innerHTML = `<strong>Ficheiro selecionado:</strong> "${fileName}"`;
	}
}