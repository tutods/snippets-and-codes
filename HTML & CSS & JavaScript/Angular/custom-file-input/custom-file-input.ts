/**
* Imports and component selector, templateUrl and styleUrls
*/	

export class MyComponent implements OnInit {
	
	/**
	 * Other methods of your component (like constructor, OnInit, ...)
	 */
	
 	/**
     * Element where show file name after choose
	 * Alternative to use document.getElementById('legend')
     */
    @ViewChild('legend') FileLegend: ElementRef;
	
	/**
	 * Method to run when have changes on input
	 */	
	showFileName(): void {
		const file = (event.target as HTMLInputElement).files[0];

		let fileName = file.name.split('\\').slice(-1).pop();

		//  Add ... if name have more than 15 chars
		if (fileName.length > 15) {
			const extension = fileName.split('.').slice(-1).pop();
			fileName = `${fileName.substr(0, 15)}(...).${extension}`;
		}
		
		this.FileLegend.nativeElement.innerHTML = `<strong>Ficheiro selecionado:</strong> "${fileName}"`;
	}
}