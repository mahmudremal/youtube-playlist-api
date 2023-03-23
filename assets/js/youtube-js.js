var popupwraper, popupbackdrop, popupcontainer, popupwrap, card, body, iframe, close, sidebar, list, item, videoId, itemlink, itemrow, itemleft, thumb, image, duration, itemright, title, metas, channel, seperator, lastmodified;
popupwraper = document.createElement( 'div' );popupwraper.classList.add( 'fwp-yt-popup' );popupwraper.setAttribute( 'title', 'Press ESC to close.' );
popupbackdrop = document.createElement( 'div' );popupbackdrop.classList.add( 'popup-backdrop' );popupwraper.appendChild( popupbackdrop );
popupcontainer = document.createElement( 'div' );popupcontainer.classList.add( 'popup-center__container' );
popupwrap = document.createElement( 'div' );popupwrap.classList.add( 'popup-center__body' );
iframe = document.createElement( 'iframe' );iframe.classList.add( 'popup-center__iframe' );
iframe.width = 560;iframe.height = 315;iframe.frameborder = 0;iframe.allowfullscreen = 1;
iframe.src = 'https://www.youtube.com/embed/yIlCqg__Nvg';popupwrap.appendChild( iframe );
sidebar = document.createElement( 'div' );sidebar.classList.add( 'popup-sidebar-right' );
list = document.createElement( 'ul' );list.classList.add( 'popup-playlist-items' );
playlist.forEach( ( e, i ) => {
    videoId = ( e.contentDetails ) ? e.contentDetails.videoId : e.snippet.resourceId.videoId;
    item = document.createElement( 'li' );item.classList.add( 'playlist-single__item' );
    itemlink = document.createElement( 'a' );itemlink.classList.add( 'playlist-single__itemlink' );
    itemlink.title = e.snippet.title;itemlink.target= '_blank';itemlink.dataset.id = videoId;
    itemlink.href = 'https://www.youtube.com/watch?v=' + videoId;
    itemrow = document.createElement( 'div' );itemrow.classList.add( 'playlist-single__itemrow' );
    itemleft = document.createElement( 'div' );itemleft.classList.add( 'playlist-single__itemleft' );
    thumb = document.createElement( 'div' );thumb.classList.add( 'playlist-single__thumb' );
    image = document.createElement( 'img' );image.classList.add( 'playlist-single__image' );
    image.src = e.snippet.thumbnails.default.url;image.alt = e.snippet.title;
    duration = document.createElement( 'div' );duration.classList.add( 'playlist-single__duration' );duration.innerHTML = '12:34';
    thumb.appendChild( image );thumb.appendChild( duration );itemleft.appendChild( thumb );itemrow.appendChild( itemleft );
    
    itemright = document.createElement( 'div' );itemright.classList.add( 'playlist-single__itemright' );
    title = document.createElement( 'h3' );title.classList.add( 'playlist-single__title' );title.innerHTML = e.snippet.title;
    metas = document.createElement( 'div' );metas.classList.add( 'playlist-single__metas' );
    channel = document.createElement( 'div' );channel.classList.add( 'playlist-single__channel' );channel.innerHTML = e.snippet.channelTitle;
    seperator = document.createElement( 'div' );seperator.classList.add( 'playlist-single__seperator' );
    lastmodified = document.createElement( 'div' );lastmodified.classList.add( 'playlist-single__lastmodified' );lastmodified.innerHTML = e.snippet.publishedAt;
    
    
    metas.appendChild( channel );metas.appendChild( seperator );metas.appendChild( lastmodified );
    itemright.appendChild( title );itemright.appendChild( metas );itemrow.appendChild( itemright );
    itemlink.appendChild( itemrow );item.appendChild( itemlink );list.appendChild( item );
} );




sidebar.appendChild( list );popupwrap.appendChild( sidebar );popupcontainer.appendChild( popupwrap );
popupwraper.appendChild( popupbackdrop );popupwraper.appendChild( popupcontainer );

document.body.appendChild( popupwraper );console.log( popupwraper );

document.querySelectorAll( '.popup-backdrop' ).forEach( ( pop ) => {pop.addEventListener( 'click', ( e ) => {e.target.parentElement.remove();} );} );
document.querySelectorAll( '.playlist-single__itemlink' ).forEach( ( item ) => {
  item.addEventListener( 'click', ( e ) => {
    e.preventDefault();
    if( item.dataset.id ) {
      iframe = document.querySelector( '.popup-center__iframe' );
      if( iframe ) {
        iframe.src = 'https://www.youtube.com/embed/' + item.dataset.id;
        document.querySelectorAll( '.playlist-single__itemlink.active' ).forEach( ( el ) => {el.classList.remove( 'active' );} );
        item.classList.add( 'active' );
      }
      else {console.log( 'signal 2' );}
    }
    else {console.log( 'signal 1' );}
  } );
} );
