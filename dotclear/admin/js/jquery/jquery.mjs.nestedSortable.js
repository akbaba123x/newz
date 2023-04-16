'use strict';(()=>{$.widget('mjs.nestedSortable',$.extend({},$.ui.sortable.prototype,{options:{tabSize:20,disableNesting:'mjs-nestedSortable-no-nesting',errorClass:'mjs-nestedSortable-error',doNotClear:false,listType:'ol',maxLevels:0,protectRoot:false,rootID:null,rtl:false,isAllowed(item,parent){return true;},},_create(){this.element.data('sortable',this.element.data('nestedSortable'));if(!this.element.is(this.options.listType))
throw new Error('nestedSortable: Please check the listType option is set to your actual list type');return $.ui.sortable.prototype._create.apply(this,arguments);},destroy(){this.element.removeData('nestedSortable').off('.nestedSortable');return $.ui.sortable.prototype.destroy.apply(this,arguments);},_mouseDrag(event){this.position=this._generatePosition(event);this.positionAbs=this._convertPositionTo('absolute');if(!this.lastPositionAbs){this.lastPositionAbs=this.positionAbs;}
const o=this.options;if(this.options.scroll){let scrolled=false;if(this.scrollParent[0]!=document&&this.scrollParent[0].tagName!='HTML'){if(this.overflowOffset.top+this.scrollParent[0].offsetHeight-event.pageY<o.scrollSensitivity)
this.scrollParent[0].scrollTop=scrolled=this.scrollParent[0].scrollTop+o.scrollSpeed;else if(event.pageY-this.overflowOffset.top<o.scrollSensitivity)
this.scrollParent[0].scrollTop=scrolled=this.scrollParent[0].scrollTop-o.scrollSpeed;if(this.overflowOffset.left+this.scrollParent[0].offsetWidth-event.pageX<o.scrollSensitivity)
this.scrollParent[0].scrollLeft=scrolled=this.scrollParent[0].scrollLeft+o.scrollSpeed;else if(event.pageX-this.overflowOffset.left<o.scrollSensitivity)
this.scrollParent[0].scrollLeft=scrolled=this.scrollParent[0].scrollLeft-o.scrollSpeed;}else{if(event.pageY-$(document).scrollTop()<o.scrollSensitivity)
scrolled=$(document).scrollTop($(document).scrollTop()-o.scrollSpeed);else if($(window).height()-(event.pageY-$(document).scrollTop())<o.scrollSensitivity)
scrolled=$(document).scrollTop($(document).scrollTop()+o.scrollSpeed);if(event.pageX-$(document).scrollLeft()<o.scrollSensitivity)
scrolled=$(document).scrollLeft($(document).scrollLeft()-o.scrollSpeed);else if($(window).width()-(event.pageX-$(document).scrollLeft())<o.scrollSensitivity)
scrolled=$(document).scrollLeft($(document).scrollLeft()+o.scrollSpeed);}
if(scrolled!==false&&$.ui.ddmanager&&!o.dropBehaviour)$.ui.ddmanager.prepareOffsets(this,event);}
this.positionAbs=this._convertPositionTo('absolute');const previousTopOffset=this.placeholder.offset().top;if(!this.options.axis||this.options.axis!='y')this.helper[0].style.left=`${this.position.left}px`;if(!this.options.axis||this.options.axis!='x')this.helper[0].style.top=`${this.position.top}px`;for(let i=this.items.length-1;i>=0;i--){const item=this.items[i];const itemElement=item.item[0];const intersection=this._intersectsWithPointer(item);if(!intersection)continue;if(itemElement!=this.currentItem[0]&&this.placeholder[intersection==1?'next':'prev']()[0]!=itemElement&&!$.contains(this.placeholder[0],itemElement)&&(this.options.type=='semi-dynamic'?!$.contains(this.element[0],itemElement):true)){$(itemElement).trigger('mouseenter');this.direction=intersection==1?'down':'up';if(this.options.tolerance=='pointer'||this._intersectsWithSides(item)){$(itemElement).trigger('mouseleave');this._rearrange(event,item);}else{break;}
this._clearEmpty(itemElement);this._trigger('change',event,this._uiHash());break;}}
const parentItem=this.placeholder[0].parentNode.parentNode&&$(this.placeholder[0].parentNode.parentNode).closest('.ui-sortable').length?$(this.placeholder[0].parentNode.parentNode):null;const level=this._getLevel(this.placeholder);const childLevels=this._getChildLevels(this.helper);let previousItem=this.placeholder[0].previousSibling?$(this.placeholder[0].previousSibling):null;if(previousItem!=null){while(previousItem[0].nodeName.toLowerCase()!='li'||previousItem[0]==this.currentItem[0]||previousItem[0]==this.helper[0]){if(previousItem[0].previousSibling){previousItem=$(previousItem[0].previousSibling);}else{previousItem=null;break;}}}
let nextItem=this.placeholder[0].nextSibling?$(this.placeholder[0].nextSibling):null;if(nextItem!=null){while(nextItem[0].nodeName.toLowerCase()!='li'||nextItem[0]==this.currentItem[0]||nextItem[0]==this.helper[0]){if(nextItem[0].nextSibling){nextItem=$(nextItem[0].nextSibling);}else{nextItem=null;break;}}}
const newList=document.createElement(o.listType);this.beyondMaxLevels=0;if(parentItem!=null&&nextItem==null&&((o.rtl&&this.positionAbs.left+this.helper.outerWidth()>parentItem.offset().left+parentItem.outerWidth())||(!o.rtl&&this.positionAbs.left<parentItem.offset().left))){parentItem.after(this.placeholder[0]);this._clearEmpty(parentItem[0]);this._trigger('change',event,this._uiHash());}
else if(previousItem!=null&&((o.rtl&&this.positionAbs.left+this.helper.outerWidth()<previousItem.offset().left+previousItem.outerWidth()-o.tabSize)||(!o.rtl&&this.positionAbs.left>previousItem.offset().left+o.tabSize))){this._isAllowed(previousItem,level,level+childLevels+1);if(!previousItem.children(o.listType).length){previousItem[0].appendChild(newList);}
if(previousTopOffset&&previousTopOffset<=previousItem.offset().top){previousItem.children(o.listType).prepend(this.placeholder);}
else{previousItem.children(o.listType)[0].appendChild(this.placeholder[0]);}
this._trigger('change',event,this._uiHash());}else{this._isAllowed(parentItem,level,level+childLevels);}
this._contactContainers(event);if($.ui.ddmanager)$.ui.ddmanager.drag(this,event);this._trigger('sort',event,this._uiHash());this.lastPositionAbs=this.positionAbs;return false;},_mouseStop(event,noPropagation){if(this.beyondMaxLevels){this.placeholder.removeClass(this.options.errorClass);if(this.domPosition.prev){$(this.domPosition.prev).after(this.placeholder);}else{$(this.domPosition.parent).prepend(this.placeholder);}
this._trigger('revert',event,this._uiHash());}
for(let i=this.items.length-1;i>=0;i--){const item=this.items[i].item[0];this._clearEmpty(item);}
$.ui.sortable.prototype._mouseStop.apply(this,arguments);},serialize(options){const o=$.extend({},this.options,options);const items=this._getItemsAsjQuery(o?.connected);const str=[];$(items).each(function(){const res=($(o.item||this).attr(o.attribute||'id')||'').match(o.expression||/(.+)[-=_](.+)/);const pid=($(o.item||this).parent(o.listType).parent(o.items).attr(o.attribute||'id')||'').match(o.expression||/(.+)[-=_](.+)/);if(res){str.push(`${o.key || res[1]}[${o.key && o.expression ? res[1] : res[2]}]`+'='+
(pid?(o.key&&o.expression?pid[1]:pid[2]):o.rootID),);}});if(!str.length&&o.key){str.push(`${o.key}=`);}
return str.join('&');},toHierarchy(options){const o=$.extend({},this.options,options);const ret=[];$(this.element).children(o.items).each(function(){const level=_recursiveItems(this);ret.push(level);});return ret;function _recursiveItems(item){const id=($(item).attr(o.attribute||'id')||'').match(o.expression||/(.+)[-=_](.+)/);if(id){const currentItem={id:id[2]};if($(item).children(o.listType).children(o.items).length>0){currentItem.children=[];$(item).children(o.listType).children(o.items).each(function(){const level=_recursiveItems(this);currentItem.children.push(level);});}
return currentItem;}}},toArray(options){const o=$.extend({},this.options,options);const sDepth=o.startDepthCount||0;const ret=[];let left=2;ret.push({item_id:o.rootID,parent_id:'none',depth:sDepth,left:'1',right:($(o.items,this.element).length+1)*2,});$(this.element).children(o.items).each(function(){left=_recursiveArray(this,sDepth+1,left);});return ret.sort((a,b)=>a.left-b.left);function _recursiveArray(item,depth,left){let right=left+1;let id;let pid;if($(item).children(o.listType).children(o.items).length>0){depth++;$(item).children(o.listType).children(o.items).each(function(){right=_recursiveArray($(this),depth,right);});depth--;}
id=$(item).attr(o.attribute||'id').match(o.expression||/(.+)[-=_](.+)/);if(depth===sDepth+1){pid=o.rootID;}else{const parentItem=$(item).parent(o.listType).parent(o.items).attr(o.attribute||'id').match(o.expression||/(.+)[-=_](.+)/);pid=parentItem[2];}
if(id){ret.push({item_id:id[2],parent_id:pid,depth,left,right});}
return right+1;}},_clearEmpty(item){const emptyList=$(item).children(this.options.listType);if(emptyList.length&&!emptyList.children().length&&!this.options.doNotClear){emptyList.remove();}},_getLevel(item){let level=1;if(this.options.listType){let list=item.closest(this.options.listType);while(list&&list.length>0&&!list.is('.ui-sortable')){level++;list=list.parent().closest(this.options.listType);}}
return level;},_getChildLevels(parent,depth=0){const o=this.options;let result=0;$(parent).children(o.listType).children(o.items).each((index,child)=>{result=Math.max(this._getChildLevels(child,depth+1),result);});return depth?result+1:result;},_isAllowed(parentItem,level,levels){const o=this.options;const isRoot=$(this.domPosition.parent).hasClass('ui-sortable')?true:false;const maxLevels=this.placeholder.closest('.ui-sortable').nestedSortable('option','maxLevels');if(!o.isAllowed(this.currentItem,parentItem)||parentItem?.hasClass(o.disableNesting)||(o.protectRoot&&((parentItem==null&&!isRoot)||(isRoot&&level>1)))){this.placeholder.addClass(o.errorClass);this.beyondMaxLevels=maxLevels<levels&&maxLevels!=0?levels-maxLevels:1;}else if(maxLevels<levels&&maxLevels!=0){this.placeholder.addClass(o.errorClass);this.beyondMaxLevels=levels-maxLevels;}else{this.placeholder.removeClass(o.errorClass);this.beyondMaxLevels=0;}},}),);$.mjs.nestedSortable.prototype.options=$.extend({},$.ui.sortable.prototype.options,$.mjs.nestedSortable.prototype.options,);})();