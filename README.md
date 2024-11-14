# Snapp Shopper

An image-based search e-commerce application.

## Table of Contents
- [Introduction](#introduction)
- [Overview](#overview)
- [Features](#feature)
- [Branching Strategy](#branching-strategy)
- [Contact Information](#contact)

## Introduction
This project is designed to target users who prioritize convenience and are keen on discovering products visually. It also aims to streamline the shopping journey by offering secure payment gateways, detailed tracking, and user-friendly interfaces for both web and mobile users

## Overview
Snapp Shopper is an innovative e-commerce platform designed to enhance the online shopping experience by incorporating advanced image recognition capabilities. Users can upload images of desired products, which the app then analyzes and matches against a broad array of items available across various online marketplaces. Through integrations with Google Vision API and major e-commerce platforms such as Alibaba, eBay, and Amazon, Snapp Shopper allows users to seamlessly discover and purchase products by searching with images instead of text.

## Features
### 1. Image-Based Product Search
- Overview: Users can search for products by uploading images, eliminating the need to type specific product names or categories.
- Technology: Integrated with Google Vision API to analyze images and search for matching items in Snapp Shopper's product database and connected e-commerce APIs.
### 2. Product Database and Cross-Platform Integrations
- Overview: Snapp Shopper is linked to multiple e-commerce APIs (e.g., Alibaba, eBay, Amazon) to display diverse product options from trusted sources.
- Technology: Leveraging API connections for real-time product information, pricing, availability, and shipping details.
### 3. Comprehensive Product Details
- Overview: Users can view detailed product information, including descriptions, pricing, reviews, ratings, and images.
- Purpose: Allows users to make informed purchasing decisions with access to comprehensive product data.
### 4. Shopping Cart and Checkout
- Features
  - Add or remove items from the cart, adjust quantities, and view total costs (including estimated shipping and tax).
  - Access to multiple payment options and secure payment gateways (e.g., Flutterwave).
  - A streamlined checkout process that integrates seamlessly with the user’s profile and payment preferences. 
### 5. Order Management and Tracking
- Order Management: Users can view their order history, track order status, and receive real-time updates on order progression.
- Delivery Tracking: Using logistics APIs (such as DHL Express), users are notified of delivery statuses and can view their package’s location on a map.
### 6. Profile and Account Management
- Overview: Enables users to manage personal details, including saved addresses, payment methods, and account settings.
- Security: Supports two-factor authentication for account security and user data protection.
### 7. Product Recommendations and Personalized Suggestions
- Overview: The app’s recommendation system provides users with personalized product suggestions based on their browsing history and previous searches.
- Benefit: Enhances the shopping experience by displaying products aligned with the user’s preferences and interests.
## 8. Customer Support and Help Desk
- Overview: Users can reach out to customer support for inquiries about orders, product information, and account assistance.
- Self-Help Resources: FAQs, troubleshooting, and order management guides.
## 3. Technology Stack
- Frontend: HTML, CSS/SCSS, JavaScript, and jQuery for web; React Native for mobile.
- Backend: PHP API for server-side processing and secure interactions with the database.
- Database: MySQL, for efficient data storage of products, user profiles, and order details.
- Third-Party Integrations:
  - Google Vision API: For image recognition.
  - E-commerce APIs: Alibaba, eBay, Amazon for product search and matching.
  - Payment Gateway: Flutterwave for secure transaction handling.
  - Logistics API: DHL Express for tracking and delivery status updates.
- AI and Machine Learning: OpenAI’s ChatGPT for enhanced search support and user assistance.

## Branching Strategy
Snapp Shopper follows a structured branching strategy to manage code development and releases effectively. We have the following branches:
- Development Branch: The development branch is where active development takes place. This is where new features, bug fixes, and hotfixes are initially developed.
- Main Branch: The main branch represents the current working release of the application. This branch is the most stable and reliable version of the project.
- Staging Branch: The staging branch is used for User Acceptance Testing (UAT). It represents the version of the application available for testing and validation before it's released to the main branch.

## Feature, Bug, and Hotfix Branches
In addition to the main branches, we create feature, bug, and hotfix branches for code development and issue resolution:
- Feature Branches: These branches are created from the development branch and are used for the development of new features or enhancements. Feature branches are merged back into the development branch once the feature is complete.
- Bug Branches: Bug branches are created from the development branch and are dedicated to addressing specific issues or bugs. Once the bug is fixed, the changes are merged back into the development branch.
- Hotfix Branches: Hotfix branches are created from the main branch and are used for addressing critical issues or bugs in the live version of the application. Once the hotfix is tested and validated, it's merged into both the development and main branches.

## Contact
If you have any questions or need further information, please raise an issue with the label of help
