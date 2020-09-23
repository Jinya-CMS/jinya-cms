use yew::prelude::*;

use crate::models::segment::Segment;

#[derive(PartialEq, Clone, Properties)]
pub struct GallerySegmentProps {
    pub segment_page_id: usize,
    pub segment: Segment,
}

pub struct GallerySegment {
    segment_page_id: usize,
    segment: Segment,
}

impl Component for GallerySegment {
    type Message = ();
    type Properties = GallerySegmentProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        GallerySegment {
            segment_page_id: props.segment_page_id,
            segment: props.segment,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.segment = props.segment;
        self.segment_page_id = props.segment_page_id;
        true
    }

    fn view(&self) -> Html {
        html! {
        }
    }
}