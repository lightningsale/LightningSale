import React from 'react';
import ReactDOM from 'react-dom'
import {BrowserRouter as Router} from "react-router-dom";
import Navigation from "./Front/Navigation";
import Header from "./Front/Header";
import Transactions from "./Front/Dashboard/Dashboard";
import Footer from "./Front/Footer";

class Front extends React.Component {
    render() {return (
        <Router basename="/front">
            <div id="page">
                <Header />
                <div className="container-fluid" style={{height: "100%"}}>
                    <div className="row" style={{height: "100%"}}>
                        <div className="col-sm-4 col-md-3 d-none d-sm-block" id="sidebar">
                            <Navigation />
                        </div>
                        <div className="col-sm-8 col-md-9" id="body" style={{height: "100%", MarginBottom: "4em"}}>
                            <Transactions/>
                            <Footer/>
                        </div>
                    </div>
                </div>
            </div>
        </Router>
    )}
}


ReactDOM.render(<Front/>, document.getElementById("root"));