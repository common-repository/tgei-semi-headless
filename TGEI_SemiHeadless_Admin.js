/**
 * This file is part of TGEI Semi Headless
 * TGEI Semi Headless is licensed with GPLv2
 * Copyright (C) 2024  Too Good Enterprises Inc.
 */

// make sure inline meta box has correct value
// based on: https://rudrastyh.com/wordpress/quick-edit-tutorial.html
jQuery( 
	function( $ )
	{
		let inlineEdit = "";
		let mode = "";
		if(typeof inlineEditPost !== "undefined")
		{
			inlineEdit = inlineEditPost;
			mode = "post";
		}
		else if(typeof inlineEditTax !== "undefined")
		{
			inlineEdit = inlineEditTax;
			mode = "tag";
		}

		// not a post or taxonomy management page
		if(inlineEdit == "")
		{
			return;
		}

		const wp_inline_edit_function = inlineEdit.edit;

		// we overwrite the it with our own
		inlineEdit.edit = function( id ) 
		{
			// let's merge arguments of the original function
			wp_inline_edit_function.apply( this, arguments );

			// get the post ID from the argument
			if ( typeof( id ) == 'object' ) { // if it is object, get the ID number
				id = parseInt( this.getId( id ) );
			}
			// add rows to variables
			const edit_row = $( '#edit-' + id );
			const table_row = $( '#' + mode + "-" + id );
			
			// get the allow list status based on the column
			const allowListStatus = "Allow" == $( '.column-tgei-semi-headless', table_row ).text() ? true : false;
			
			// populate the inputs with column data
			$("input[id='tgei-semi-headless-allow_ui']", edit_row).prop("checked", allowListStatus);
		};

		if(mode == "post")
		{
			inlineEditPost.edit = inlineEdit.edit;
		}
		else
		{
			inlineEditTax.edit = inlineEdit.edit;
		}
	}
);

//check if we have a bulk action selector
let bulkActionSelect = document.getElementById("bulk-action-selector-top");
if(bulkActionSelect != null)
{
	// get all the option values
	let bulkActionOptions = bulkActionSelect.getElementsByTagName("option");

	// find the options for TGEI Semi Headless
	let tgeiSemiHeadlessOptions = [];
	for(let i = 0; i < bulkActionOptions.length; i++)
	{
		let o = bulkActionOptions[i];
		if(o.value.indexOf("tgei-semi-headless") == 0)
		{
			tgeiSemiHeadlessOptions.push(o);
		}
	}
	// Create optgroup for TGEI Semi Headless
	if(tgeiSemiHeadlessOptions.length > 0)
	{
		let optGroup = document.createElement("optgroup");
		optGroup.setAttribute("label","TGEI Semi Headless");
		for(let i = 0; i < tgeiSemiHeadlessOptions.length; i++)
		{
			let o = tgeiSemiHeadlessOptions[i];

			// shorten the option label since it's now in an optgroup
			if(o.value.indexOf("allow") != -1)
			{
				o.innerText = "Allow";
			}
			else if(o.value.indexOf("block") != -1)
			{
				o.innerText = "Block";
			}
			optGroup.appendChild(o);
		}
		
		// add optgroup to the select
		bulkActionSelect.appendChild(optGroup);
	}
}

// check if we have a column sorter
let columnSorter = document.getElementsByClassName("column-tgei-semi-headless");
if(columnSorter != null)
{
	if(columnSorter.length > 0)
	{
		let sortLink = columnSorter[0].getElementsByTagName("a")[0];
		let sortUrl = sortLink.getAttribute("href");
		sortLink.setAttribute("href", sortUrl + "&tgei-semiheadless-nonce=" + TGEI_SemiHeadless_Data.nonce);
	}
}
