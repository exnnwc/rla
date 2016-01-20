#RLA 
This is a project I'm working on called Real Life Achievements. RLA is the working title. As the title implies, I' basically trying to implement video game achievements in real life.

I'm trying to avoid any kind of overt gamification like some of the other implementations where they literally try to make the achievements process like a video game. I want it to be more like social media. Kind of like how Instagram is a place where you can represent yourself in the most egoticistically fulfilling fashion with pictures. This continues that process. To basically brag about all the stuff that you've accomplished. 


One of the most important features that I would like to develop for RLA is to be able to track the progress of a particular achievement. That is paramount, especially in larger achievements that may take years. Being able to track your progress helps to not become disheartened. 

Just thinking out loud right now, because I am having some design issues with the work queue.

I know there needs to be a centralized place to track progress on achievements. I know that actions play a role in that.

There's the queue which displays all achievements that are listed as being available to work on and their corresponding actions then there's the work log which logs whether or not those achievements or actions have been worked on.

Queue.
Displays all achievements that are currently able to be worked on and their corresponding actions.  When you register an action as being worked on, that progresses the achievement towards being worked on. I would like to eventually implement a logic system to the actions. For example:

Be physical fit. (Daily)
BEGIN GROUP 1
-Exercise
AND 
-Log and limit caloric intake.
END GROUP 1
OR 
-Log and limit caloric intake.

This will be an implementation in the future. For now I think it will suffice to make the actions either all AND or OR. 

Honestly, the code is a mess right now and I'm continually putting off the refactor because I'm trying to finish off this last bit, but I might need to just start refactoring now. (01/20/16)

Work Log
Lists all actions that have been worked and all actions that have not been worked during their respective time periods.

It's important that I be able to specify successfully worked actions and non-worked actions. It's also important to be able to set the time context. (DAILY, WEEKLY, MONTHLY, etc.)

TODO

FEATURES


