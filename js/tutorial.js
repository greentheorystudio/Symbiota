function hideTutorial() {
	document.getElementById('hideToggle').style.display = 'none';
	document.getElementById('showToggle').style.display = 'block';
	const topicTitleElements = document.getElementsByClassName('topic-title-slide');
	for(let i in topicTitleElements){
		if(topicTitleElements.hasOwnProperty(i)){
			topicTitleElements[i].style.display = 'none';
		}
	}
	const topicConentElements = document.getElementsByClassName('topic-content-slide');
	for(let i in topicConentElements){
		if(topicConentElements.hasOwnProperty(i)){
			topicConentElements[i].style.display = 'none';
		}
	}
}

function showTutorial() {
	document.getElementById('hideToggle').style.display = 'block';
	document.getElementById('showToggle').style.display = 'none';
	const topicTitleElements = document.getElementsByClassName('topic-title-slide');
	for(let i in topicTitleElements){
		if(topicTitleElements.hasOwnProperty(i)){
			topicTitleElements[i].style.display = 'block';
		}
	}
	const topicConentElements = document.getElementsByClassName('topic-content-slide');
	for(let i in topicConentElements){
		if(topicConentElements.hasOwnProperty(i)){
			topicConentElements[i].style.display = 'block';
		}
	}
}
